<?php
error_reporting(E_ALL);
/*
 * 报表基础类 
 * @package        action
 * @author          feng.yin <feng.yin@baifendian.com>
 * @copyright       Copyright (c) 	2011
 * @version         $id: V0.6
 */
class ReportAction extends BaseAction {
    function _initialize() {
        parent::_initialize();
    }

    function index() {
    	//绑定设备的资源使用情况
    	$device_use_total_count = M('')->field('count(s.id) count, s.state, d.model')
				      ->table('source s')
				      ->join('device d ON s.mark = d.mark')
				      ->group('d.model,s.state')
				      ->select();
    	$device_use_total = array();
    	foreach ($device_use_total_count as $v) {
    		$device_use_total[$v['model']][$v['state']] = $v['count'];
    	}
    	//设备本身状态汇总
    	$device_state_total_count = M('')->field('count(d.id) count, md.state, d.model')
								    	->table('device d')
								    	->join('monitor_device md ON md.deviceid = d.id')
								    	->group('d.model,md.state')
								    	->select();
		$device_state_total = array();
    	foreach ($device_state_total_count as $v) {
    		$device_state_total[$v['model']][$v['state']] = $v['count'];
    	}
    	//所以设备汇总
    	$device_total = array_group_by(M('device')->field('count(*) count, model')->group('model')->select(), 'model', false);
    	$device_frequncy = array_group_by(M('event')->field('count(*) count, mark')->group('mark')->order('`count` desc')->limit(5)->select(), 'mark', false);
    	
    	$date = array('2016-07-08','2016-07-09','2016-07-10');
    	$data = array();
    	$i = 0;
    	foreach ($device_frequncy as $k=>$v) {
    		$data[$i]['name'] = $k;
    		foreach ($date as $d) {
    			$data[$i]['data'][] = mt_rand(1, 200);
    		}
    		$i++;
    	}
    	
    	$this->assign(array(
    		'title'=>"设备洞察",
    		'device_total'=>$device_total,
    		'device_use_total'=>$device_use_total,
    		'device_state_total'=>$device_state_total,
    		'x'=>$date,
    		'data'=>$data,
    		//'device_frequncy'=>$device_frequncy,
    		'__curent_model__' => $this->_curent_model,
    		'__curent_action__' => $this->_curent_action,
    	));
    	$this->display('Report/index.html');
    }

    function app() {
    	//总量
    	$user = M('user')->count();
    	$start = strtotime(date('Y-m-d',time()));
    	$today_user = M('user')->where('createtime>'.$start)->count();
    	
    	//男女占比
    	$sex_proportion = array_group_by(M('user')->field('count(id) count, sex')->group('sex')->select(), 'sex', false);
    	foreach ($sex_proportion as $k=>$v) {
    		$sex_proportion[$k]['proportion'] = $v['count'] * 100 / $user;
    	}
    	
    	//设备本身状态汇总
    	$app_resource_temp = M('')->query('SELECT SUM(`count`) as `count`,`text` FROM (SELECT COUNT(e.id) `count`, e.mark, d.text FROM `event` e LEFT JOIN `source` s  ON  s.`mark` = e.`mark` LEFT JOIN `dict` d  ON  d.`textid` = s.`textid` WHERE s.`mark` IS NOT NULL GROUP BY e.`mark` ORDER BY `count` DESC) AS sou GROUP BY `text`');
    	$app_resource_count = array();
    	foreach ($app_resource_temp as $v) {
    		$app_resource_count['x'][] = $v['text'];
    		$app_resource_count['data'][] = (int)$v['count'];
    	}
    	
    	$date = array('2016-07-08','2016-07-09','2016-07-10');
    	$data = array();
    	$i = 0;
    	foreach ($app_resource_count['x'] as $v) {
    		$data[$i]['name'] = $v;
    		foreach ($date as $d) {
    			$data[$i]['data'][] = mt_rand(1, 200);
    		}
    		$i++;
    	}
    	
    	$this->assign(array(
    		'title'=>"app洞察",
    		'user_count'=>array('all'=>$user, 'today'=>$today_user),
    		'sex_proportion'=>$sex_proportion,
    		'app_resource_count'=>$app_resource_count,
    		'x'=>$date,
    		'data'=>$data,
    		'__curent_model__' => $this->_curent_model,
    		'__curent_action__' => $this->_curent_action,
    	));
    	$this->display('Report/app.html');
    }
    
    function make() {
    	/*
    	 //add monitor_app
    	$source = M('source')->select();
    	$time = time();
    	$i = 20000;
    	while( $i>0 ) {
    	shuffle($source);
    	M('monitor_app')->add(
    		array('uid'=>4,'createtime'=>$time-$i,'sourceid'=>$source[0]['id'])
    	);
    	$i -= 3600;
    	}
    	*/
    	
    	/* 修改source/device
    	 //b8:27:eb:e0:e1:cf_2
    	$source = M('source')->select();
    	foreach ( $source as $v) {
    		M('source')->where('id='.$v['id'])->save(
    			array('mark'=>'b8:27:eb:e0:e1:cf_' . $v['id'])
    		);
    	}
    	$device = M('device')->select();
    	foreach ( $device as $v) {
    		M('device')->where('id='.$v['id'])->save(
    		array('mark'=>'b8:27:eb:e0:e1:cf_' . $v['id'])
    		);
    	}
    	*/
    	
    	/* add user 
    	$sex = array(0,1);
    	$location = array('A','B','C','D','E','F');
    	for( $i=20;$i<100;$i++ ) {
    		M('user')->add(array(
    			'sex'=>mt_rand(0, 1),
    			'location'=>$location[mt_rand(0, 5)],
    			'advanced'=>mt_rand(0, 1),
    			'mac'=>'test' . $i,
    			'createtime'=>time(),
    			'modifytime'=>time(),
    			'token'=>'test' . $i,
    		));
    	}
    	*/
    	
    	/* add remind 
    	$source = M('source')->select();
    	$user = M('user')->select();
    	foreach ($user as $_user) {
    		$u = $_user['id'];
    		$time = mt_rand(1,4);
    		shuffle($source);
    		for($t=0;$t<$time;$t++) {
    			M('remind')->add(array(
	    			'userid'=>$u,
	    			'sourceid'=>$source[$t]['id'],
	    			'state'=>mt_rand(0, 1),
	    			'createtime'=>time(),
    			));
    		}
    	}
*/
    	
    }
}
?>