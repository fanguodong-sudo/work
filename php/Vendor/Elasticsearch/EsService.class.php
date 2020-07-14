<?php

namespace BaseSoa\Vendor\Elasticsearch;
class EsService {
    /**
     * @var string
     */
    private $indexName;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $pass;
    /**
     * @var string
     */
    private $hosts;
    private $client;
    private $where;
    private static $instance = [];


    function __construct($host='', $port='', $indexName='', $tableName='', $user='', $pass=''){
        $config     = C("DB_CONFIG_ELASTICSEARCH");
        $config     = [
            'DB_TYPE'  => 'elasticsearch',
            'DB_HOST'  => ['192.168.0.187:9200'],
            'DB_INDEX' => 'base_sherpas',
            'DB_TABLE' => 'es_keyword',
            'DB_USER'  => 'daojia123',
            'DB_PASS'  => 'daojia123'
        ];

        $this->indexName = $indexName?$indexName:$config['DB_INDEX'];
        $this->tableName = $tableName?$tableName:$config['DB_TABLE'];
        $this->user   = $user?$user:$config['DB_USER'];
        $this->pass   = $pass?$pass:$config['DB_PASS'];
        $this->hosts  = $host?$host:$config['DB_HOST'];
        $hosts        = $this->hosts;
        $this->client = self::getInstance($hosts);
        $this->setLimit();
        $this->where['index']   = $this->indexName;
        $this->where['type']    = $this->tableName;
        $this->where['client']  = ['future' => 'lazy'];
    }

    public static function getInstance($config=[]){
        $md5 = md5(serialize($config));
        if(!isset(self::$instance[$md5])){
            require_once __DIR__ . '/vendor/autoload.php';
            self::$instance[$md5] = \Elasticsearch\ClientBuilder::create()
                ->setHosts($config)// Set the hosts
                ->build(); // Build the client object
        }
        return self::$instance[$md5];
    }

    public function setTableName($tableName){
        $this->tableName = $tableName;
        $this->where['type'] = $tableName;
    }

    public function setIndexName($indexName){
        $this->indexName = $indexName;
        $this->where['index'] = $indexName;
    }

    /**返回字段
     * @param $sourceName
     */
    public function setSource($sourceName){
        $this->where['body']['_source'] = explode(',',$sourceName);
    }

    /**查询数据
     * @param $data
     * @return array
     */
    public function search($data){

        if(isset($data['highLight'])){
            $this->where['body']['highLight'] = $data['highLight'];
        }

        $this->where['body']['query'] = $data['query'];
        $list = $this->client->search($this->where);
        $this->where['body'] = [];
        $data = $list['hits'];
        $res  = [];
        foreach($data['hits'] as $v){
            $res['hits']['hits'][] = $v;
        }
        return $res;

    }

    /**设置排序
     * @param $sort
     */
    public function setSort($sort){
        $this->where['body']['sort'] = $sort;
    }

    /** 设置条数
     * @param int $from 开始条数
     * @param int $size 数量
     */
    public function setLimit($from=0, $size=10){
        $this->where['body']['from'] = $from;
        $this->where['body']['size'] = $size;
    }

}

/*
 *
 * public function example(){
        $es = new EsService();
        $queryData['query']['match_phrase']['Keyword'] = '肉';
        $queryData['sort'][]['word_weight']['order']   = 'asc';
        $es->setLimit(0,10);
        $r = $es->search($queryData);
        print_r($r);
    }
 *
 * */