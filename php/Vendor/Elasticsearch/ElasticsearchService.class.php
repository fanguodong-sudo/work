<?php

/**
 * @Author: 沈鑫
 * @Date:   2018-02-09 16:29:37
 * @Last Modified by:   沈鑫
 * @Last Modified time: 2018-04-27 16:42:15
 */
namespace BaseSoa\Vendor\Elasticsearch;

class ElasticsearchService {
    private $host;
    private $port;
    private $indexName; //索引名，类似于数据库中的数据库名
    private $tableName; //索引的表名，类似于数据库中的数据库的表名
    private $user; //索引名，类似于数据库中的数据库名
    private $pass; //索引的表名，类似于数据库中的数据库的表名
    private $client; //索引的表名，类似于数据库中的数据库的表名
    private $where; //索引的表名，类似于数据库中的数据库的表名
    static $instance = [];

    public function __construct($host = "", $port = "", $indexName = "", $tableName = "", $user = '', $pass = '') {

        $config     = C("DB_CONFIG_ELASTICSEARCH");
        $this->host = $host ? $host : $config['DB_HOST'];
        $this->indexName = $indexName ? $indexName : $config['DB_INDEX'];
        $this->tableName = $tableName ? $tableName : $config['DB_TABLE'];
        $this->user      = $user ? $user : $config['DB_USER'];
        $this->pass      = $pass ? $pass : $config['DB_PASS'];
        $hosts           = $this->host;
        $this->client    = self::getInstance($hosts);

        $this->Setlimit();
        $this->where['index'] = $this->indexName;
        $this->where['type']  = $this->tableName;
        $this->where['client'] = ['future' => 'lazy'];
    }

    public static function getInstance($config = array()) {
        $md5 = md5(serialize($config));
        if (!isset(self::$instance[$md5])) {
            require_once __DIR__ . '/vendor/autoload.php';

            self::$instance[$md5] = \Elasticsearch\ClientBuilder::create() // Instantiate a new ClientBuilder
                ->setHosts($config) // Set the hosts
                ->build(); // Build the client object
        }
        return self::$instance[$md5];
    }
    /**
     * 设置表明
     */
    public function setTableName($tableName) {

        $this->tableName = $tableName;

        $this->where['type'] = $this->tableName;
    }
    /**
     * 设置索引名
     */
    public function setIndexName($IndexName) {
        $this->indexName      = $IndexName;
        $this->where['index'] = $this->indexName;
    }

    /**
     * 设置返回字段
     */
    public function SetSource($SourceName) {

        $this->where['body']['_source'] = explode(',', $SourceName);

    }

    /**
     * 设置返回字段
     */
    public function SetSort($Sort) {

        $this->where['body']['sort'] = $Sort;

    }

    /**
     * 设置返多少行
     */
    public function Setlimit($offict = 0, $limit = 20) {
        $this->where['body']['from'] = $offict;
        $this->where['body']['size'] = $limit;

    }

    /**
     * 查找数据
     *
     * @param array $data
     */
    public function search($data) {
        if (isset($data['highlight'])) {
            $this->where['body']['highlight'] = $data['highlight'];
        }
        $this->where['body']['query'] = $data['query'];
//        echo json_encode($this->where);die;
        $list                         = $this->client->search($this->where);

        $this->where['body'] = [];
        $data                = $list['hits'];

        $res = [];
        foreach ($data['hits'] as $v) {

            $res['hits']['hits'][] = $v;
        }

        return $res;

    }

}
