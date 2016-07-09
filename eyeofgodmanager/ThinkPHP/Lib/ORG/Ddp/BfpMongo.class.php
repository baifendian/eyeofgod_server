<?php

/**
 * 文件名             MONGO操作类
 * 文件内容简介       对MONGODB的增删改查
 * @package         ORG
 * @author          liutao <liutao@baifendian.com>
 * @copyright       Copyright (c) 	2011
 * @license         New BSD License {@link http://www.izptec.com/license/}
 * @version         $id: V0.5
 */
class BfpMongo {

    /**
     * 类的成员变量
     * param              Mongo           $mongo         mongo的对象
     * param              String          $db            mongo的数据库           
     */
    var $mongo;
    var $db;
    var $ddp;

    function __construct($uri='', $db='') {
        $uri = empty($uri) ? C("MONGO_URI") : $uri;
        $db = empty($db) ? C("MONGO_DB") : $db;
        $this->mongo = new Mongo($uri);
        $this->db = $db;
        $this->ddp = new ddp();
    }

    /**
     * 获取MONGO表中数据的最新更新时间
     * param              String          $table         MONGO的表名
     * param              String          $db            mongo的数据库
     * return             int             $max['date']   时间戳
     */
    function getMaxDateTime($table, $websiteid) {
        $db = $this->db;
        $collection = $this->mongo->$db->$table;
        $time = $collection->find(array("websiteid" => $websiteid))->sort(array("date" => -1))->limit(1);
        $max = $time->getNext();
        return $max['date'];
    }

    /**
     * 根据检索条件返回检索结果
     * param              Array          $search         搜索条件数组
     * return             Array                          结果集
     */
    function getSearchResults($search = array()) {
        
    }

    /**
     * 根据weisiteid把从DDP检索过来的数据写入到MONGO中
     * param              String          $websiteid        从DDP检索过来的数据
     * return             BOOL                              插入状态
     */
    function insert_into_media_for_day($websiteid) {
        $collection = $this->returnCollectionHandle("test");
        if (empty($websiteid)) {
            return false;
        } else {
            $data = $this->ddp->get_site_info("website", $websiteid);
            $info = json_decode($data);
            $site = $info->data;
            if (!empty($site)) {
                foreach ($site as $val) {
                    $d = array(
                        "websiteid" => $v,
                        "channelid" => $val->Channelid,
                        "adposid" => $val->Adposid,
                        "pv" => $val->PV,
                        "ip" => $val->IP,
                        "ck" => $val->CK,
                        "areaid" => $val->Areaid,
                        "date" => $val->Date
                    );
                    $collection->insert($d);
                }
                return true;
            }
            return 0;
        }
    }

    /**
     * 根据websiteid对MONGO中已有的数据进行更新
     * param              String          $websiteid      网站ID
     * param              INT             $today       当天时间戳
     * return             BOOL                            更新状态
     */
    function update_to_media_for_day($websiteid, $today) {
        if (isset($websiteid)) {
            $collection = $this->returnCollectionHandle("test");
            $data = $this->ddp->get_site_info("website", $websiteid);
            $info = json_decode($data);
            $site = $info->data;
            if (!empty($site)) {
                foreach ($site as $val) {
                    $search = array(
                        "websiteid" => $websiteid,
                        "channelid" => $val->Channelid,
                        "adposid" => $val->Adposid,
                        "areaid" => $val->Areaid,
                        "date" => array('$gt' => $today)
                    );
                    $replace = array(
                        "pv" => $val->PV,
                        "ip" => $val->IP,
                        "ck" => $val->CK,
                    );
                    $options['multiple'] = false;
                    $collection->update($search, $replace, $options);
                } return true;
            } return false;
        } return false;
    }

    function parseSearch($search) {
        $res = $range = array();
        if (!empty($search)) {
            foreach ($search as $key => $val) {
                if ($key == "start_date" || $key == "end_date") {
                    if ($key == "start_date") {
                        $range['$gt'] = $val;
                    } else if ($key == "end_date") {
                        $range['$lt'] = $val;
                    }
                    $res['date'] = $range;
                } else {
                    $res[$key] = $val;
                }
            }
        }
        return $res;
    }

    function returnCollectionHandle($tabName='') {
        $tabName = empty($tabName) ? "test" : trim($tabName);
        $db = $this->db;
        return $this->mongo->$db->$tabName;
    }

    function returnDBHandle($db='') {
        $db = empty($db) ? $this->db : $db;
        return $this->mongo->selectDB($db);
    }

    function getResultWithCondition($search='', $tabName='', $limit='') {
        $collection = $this->returnCollectionHandle($tabName);
        $rs = empty($search) ? $collection->find()->limit(100) : $collection->find($search);
        $results = array();
        while ($rs->hasNext()) {
            $r = array();
            $res = $rs->getNext();
            foreach ($res as $key => $val) {
                if ($key != "_id") {
                    $r[$key] = $val;
                }
            }
            $results[] = $r;
        }
        return $results;
    }

    function getGroupResults() {
        $collection = $this->returnCollectionHandle("test");
        $websites = $collection->distinct(array("websiteid"));
        $counts = array("ip" => 0);
        $reduce = "function (obj, prev) { prev.sum += 1; }";
        $result = $collection->group(array("websiteid" => true), $counts, $reduce);
        return $result;
    }

    function get_group_results($search, $websiteid) {
        $db = $this->returnDBHandle();
        $results = $final = array();
        $map = new MongoCode("function() { emit(this.websiteid,{ip:this.ip,pv:this.pv,ck:this.ck,ep:this.ep}); }");
        $reduce = new MongoCode("function(k, vals) { " .
                        "var pv = 0,ip = 0, ck = 0,ep=0;" .
                        "for (var i in vals) {" .
                        "pv += parseInt(vals[i].pv);" .
                        "ip += parseInt(vals[i].ip);" .
                        "ck += parseInt(vals[i].ck);" .
                        "ep += parseInt(vals[i].ep);" .
                        "}" .
                        "return pv+','+ip+','+ck+','+ep; }");

        $datasets = $db->command(array(
                    "mapreduce" => "test",
                    "map" => $map,
                    "reduce" => $reduce,
                    "query" => $search,
                    "out" => array("merge" => "tempdb")
                        )
        );
        $sets = $db->selectCollection($datasets['result'])->find(array("_id" => $websiteid));
        $info = $db->selectCollection("test")->find(array("websiteid" => $websiteid))->limit(1);
        $siteinfo = array();
        while ($info->hasNext()) {
            $siteinfo[] = $info->getNext();
        }
        //print_r($siteinfo);
        foreach ($sets as $set) {
            $results = $set;
        }
        //print_r($results);
        if (!empty($results['value'])) {
            $totalinfo = $this->getTotalInfo($results['value']);
            // print_r($totalinfo);

            $siteinfo[0]['pv'] = $totalinfo[0];
            $siteinfo[0]['ip'] = $totalinfo[1];
            $siteinfo[0]['ck'] = $totalinfo[2];
            $siteinfo[0]['ep'] = $totalinfo[3];
        }
        if (!empty($siteinfo[0])) {
            foreach ($siteinfo[0] as $key => $site) {
                if ($key != "_id") {
                    $final[$key] = $site;
                }
            }
        }
        return $final;
    }

    function get_group_interest_results($search, $websiteid) {
        $db = $this->returnDBHandle();
        $results = $final = array();
        $map = new MongoCode("function() { emit(this.intersting,{ip:this.ip,pv:this.pv,ck:this.ck,ep:this.ep}); }");
        $reduce = new MongoCode("function(k, vals) { " .
                        "var pv = 0,ip = 0, ck = 0,ep=0;" .
                        "for (var i in vals) {" .
                        "pv += parseInt(vals[i].pv);" .
                        "ip += parseInt(vals[i].ip);" .
                        "ck += parseInt(vals[i].ck);" .
                        "ep += parseInt(vals[i].ep);" .
                        "}" .
                        "return pv+','+ip+','+ck+','+ep; }");

        $datasets = $db->command(array(
                    "mapreduce" => "interest",
                    "map" => $map,
                    "reduce" => $reduce,
                    "query" => $search,
                    "out" => array("merge" => "tempdb")
                        )
        );
        $sets = $db->selectCollection($datasets['result'])->find(array("_id" => $websiteid));
        $info = $db->selectCollection("interest")->find(array("websiteid" => $websiteid))->limit(1);
        $siteinfo = array();
        while ($info->hasNext()) {
            $siteinfo[] = $info->getNext();
        }
        //print_r($siteinfo);
        foreach ($sets as $set) {
            $results = $set;
        }
        //print_r($results);
        if (!empty($results['value'])) {
            $totalinfo = $this->getTotalInfo($results['value']);
            // print_r($totalinfo);

            $siteinfo[0]['pv'] = $totalinfo[0];
            $siteinfo[0]['ip'] = $totalinfo[1];
            $siteinfo[0]['ck'] = $totalinfo[2];
            $siteinfo[0]['ep'] = $totalinfo[3];
        }
        if (!empty($siteinfo[0])) {
            foreach ($siteinfo[0] as $key => $site) {
                if ($key != "_id") {
                    $final[$key] = $site;
                }
            }
        }
        return $final;
    }

    private function getTotalInfo($values) {
        if (!empty($values)) {
            $res = explode(",", $values);
            return $res;
        } else {
            return false;
        }
    }

    private function getOne($websiteid, $tabName) {
        $col = $this->returnCollectionHandle($tabName);
        $rs = $col->findOne(array("websiteid" => $websiteid));
        var_dump($rs);
    }

    function insert_into_media_from_mysql($data, $today) {
        if (!empty($data)) {
            
        }
    }

}

?>
