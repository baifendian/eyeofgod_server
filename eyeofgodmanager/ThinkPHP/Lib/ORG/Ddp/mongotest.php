<?php
ini_set('mongo.native_long', 1);
/* $mongo = new Mongo("localhost:20004");
  $db = $mongo->dbbfp->test;
  $d = $db->find()->sort(array("date"=>-1))->limit(1);
  $maxdate = $d->getNext();
  echo $maxdate['date'];
  exit;
  //$d = $d->sort(array("date"=>-1));
  while($d->hasNext()){
  $r = $d->getNext();
  echo $r['date']."<br>";
  } */

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

$search = array("websiteid"=>"12f9","channelid"=>"295d","start_date"=>"1310008800","end_date"=>"1310008840");

$res = parseSearch($search);


$mongo = new Mongo("localhost:20004");
$db = $mongo->dbbfp->test;
$result = $db->group(
 array("websiteid" => true),
 array("sum" => 0),
 "function (obj, prev) { prev.sum += 1; }");
var_dump($result);
exit;
//$search = array("websiteid" => "480a",);
$data = $db->find($res)->sort(array("date"=>-1));
$sets = array();
while ($data->hasNext()) {
    $sets[] = $data->getNext();
}
print_r($sets);
?>