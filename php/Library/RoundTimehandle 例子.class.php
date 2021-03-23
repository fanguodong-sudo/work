<?php
namespace OpenApi\Service;
use Dj\Client;
use OpenApi\Base\Model\BaseModel;

class RoundTimeService  extends BaseModel
{
    protected $autoCheckFields = false;
    public function _initialize(){
        parent::_initialize();
        $this->RoundTimeMongo = D('RoundTime', 'Mongo');
    }

    //根据type  //Time，AddKey，TouchKey (三个都需要实现)
    public function getSetting($prefix,$type){
        return newS($prefix.$type);
    }

    //根据type  //Time，AddKey，TouchKey (三个都需要实现)
    public function setSetting($prefix,$value,$type){
        return newS($prefix.$type,$value);
    }

    //设置成功
    function setSuccess($id,$log){
        return $this->RoundTimeMongo->successRoundTime($id,$log);
    }

    //添加任务
    function add($data){
//        $param = json_decode($data['param'],1);
//        $data = json_decode($param['strParameter'],1);
        return $this->RoundTimeMongo->addRoundTime($data);
    }

    //获取key
    function getData($key,$onlyCount = false){
        $param = [];
        $param['key'] = $key;
        $param['status'] = 1;
        $result = $this->RoundTimeMongo->getRoundTimeListByCondition($param,$onlyCount);
        if($onlyCount){
            return $result['all_count'];
        }
        return $result;
    }
}