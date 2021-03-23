<?php

namespace Library;
class RoundTimer{
    public $config = [
        'isBlockTotal' => true,
        'blockTotal' => 2,
        'blockTime' => 1,
        'AllTime' => 60,
        'type' => self::LINE
    ];
    const CIRCLE = 1;
    const LINE = 2;
    /*
        1圆圈轮询
        currenKey 计算规则 floor（（当前时间-固定时间）% AllTime）/blockTime）

        2线性轮询
        currentKey 计算规则 固定时间为第一个key，如果第一个key的数量>=1000,key+1,更新addKey（time添加key）
        touchTime 处理过程中处理到哪个key
    */

    public $prefix;

    public function __construct($prefix){
        $this->prefix = $prefix;
    }

    public function setConfig($config=null){
        if($config){
            $this->config = $config;
        }
    }
    public $handler;
    public function touch($func,$currentKey=null){
        if($this->config['type'] == self::LINE){
            if($currentKey==null){
                $touchKey = $this->getSetting("TouchKey");
            }else{
                $touchKey = $currentKey;
            }
            $count = $this->handler->getData($this->prefix.$touchKey,true);
            if($count == 0){
                $touchKey += 1;
                $this->handler->setSetting($this->prefix,$touchKey,'TouchKey');
            }
        }else{
            if($currentKey==null){
                $currentKey = $this->getCurrentKey();
            }
            if($currentKey == 0){
                $touchKey = floor($this->config['AllTime']/$this->config['blockTime']);
            }else{
                $touchKey = $currentKey - 1;
            }
        }

        $touchKey = $this->prefix.$touchKey;
        $data = $this->handler->getData($touchKey);
        $func($data);
    }

    public function setSuccess($id,$log=''){
        $this->handler->setSuccess($id,$log);
    }

    public function setHandler($handler){
        $this->handler = $handler;
        if(!$this->handler->getSetting($this->prefix,"Time")){
            $this->handler->setSetting($this->prefix,"Time");
        }
    }

    public function getCurrentKey(){
        $time = time() - $this->handler->getSetting($this->prefix,"Time");
        return floor(($time%$this->config['AllTime'])/$this->config['blockTime']);
    }

    public function getSetting($type="AddKey"){
        if($addKey = $this->handler->getSetting($this->prefix,$type)){
            return $addKey;
        }else{
            $addKey = $this->handler->getSetting($this->prefix,"Time");
            $this->handler->setSetting($this->prefix,$addKey,$type);
            return $addKey;
        }
    }

    public function add($data){
        if($this->config['type'] == self::LINE){
            $data['key'] = $this->getSetting("AddKey");
            $count = $this->handler->getData($this->prefix.$data['key'],true);
            if($count >= $this->config['blockTotal']){
                $data['key'] += 1;
                $this->handler->setSetting($this->prefix,$data['key'],"AddKey");
            }
            $data['key'] = $this->prefix.$data['key'];
        }else{
            $data['key'] = $this->prefix.$this->getCurrentKey();
        }

        $data['status'] = 1;

        //判断是否开启条数
        if($this->config['isBlockTotal']){
            $count = $this->handler->getData($data['key'],true);
            if($count >= $this->config['blockTotal']){
                return ['status'=>false,'errorNo'=>1001];
            }
        }
        return $this->handler->add($data);
    }
}

/*
 *      调用例子
 *      $timer = new RoundTimer('openApiLine');
        $timer->setConfig([
            'blockTotal' => 2,
            'blockTime' => 60,
            'type' => 2
        ]);
        $timer->setHandler($this->roundTimeService);
        $timer->touch(function($data) use($timer) {
            //你的代码
        },data);
-----------------------
        //添加任务例子
        $timer = new RoundTimer('openApiLine');
        $timer->setConfig([
            'blockTotal' => 2,
            'blockTime' => 60,
            'type' => 2
        ]);
        $timer->setHandler($this->roundTimeService);
        $timer->add([
            'param' => json_encode($this->params),
            'url' => $_SERVER['HTTP_HOST']."/OpenApi/".$_SERVER['PATH_INFO'],
            'callBackUrl' => "callBack" //todo
        ]);
 * */