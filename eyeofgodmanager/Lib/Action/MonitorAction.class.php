<?php
import('@.Action.Base');

error_reporting(E_ALL);

class MonitorAction extends BaseAction {
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index() {
		$this->assign(array(
			'title'=>"设备监控",
			'__curent_model__' => $this->_curent_model,
			'__curent_action__' => $this->_curent_action,
			'list' => true,
		));
		$this->display('Monitor/index.html');
	}
	
	public function app() {
		$source = M('source')->select();
		$this->assign(array(
			'title'=>"app监控",
			'__curent_model__' => $this->_curent_model,
			'__curent_action__' => $this->_curent_action,
			'list' => true,
		));
		$this->display('Monitor/app.html');
	}
	
	public function ajaxGetList() {
		$state = array("使用","<span class='red'>空闲</span>");
		$dstate = array("<span class='red'>异常</span>","正常");
		if( !is_post() || !is_ajax_call() ) exit('__HALT__');
		$post = html_encode($_POST);
		$table = $post['table'];
		$page = to_int($_REQUEST['page']);
		$limit = to_int($_REQUEST['rows']);
		$sidx = strtolower($_REQUEST['sidx']);
		$sord = strtolower($_REQUEST['sord']);
		$sord = !in_array($sord, array('desc','asc'))?'desc':$sord;
		$count = M('')->field('md.state dstate,d.name,d.mark,s.location,dt.text')
						  ->table('monitor_device md')
						  ->join('device d ON d.id = md.deviceid')
						  ->join('source s ON s.mark = d.mark')
						  ->join('dict dt ON dt.textid = s.textid')
						  ->count();
		$pageinfo = parse_page($count, $limit, $page);
		if ($count > 0) {
			$total_pages = ceil($count / $limit);
		} else {
			$total_pages = 0;
		}
		if ( $count > 0 ) {
			$start = $pageinfo['limit']['start'];
			$end = $pageinfo['limit']['end'];
			$result = M('')->field('md.state dstate,d.name,d.mark,d.brand,s.location,s.state,dt.text')
						  ->table('monitor_device md')
						  ->join('device d ON d.id = md.deviceid')
						  ->join('source s ON s.mark = d.mark')
						  ->join('dict dt ON dt.textid = s.textid')
						  ->order("{$sidx} {$sord}")->limit("{$start},{$end}")->select();
			foreach ($result as $k=>$row) {
				$temp_data['id'] = $k;
				$temp_data['mark'] = $row['mark'];
				$temp_data['name'] = $row['brand'] . '-' .$row['name'];
				$temp_data['source'] = $row['location'] . '区-' .  $row['text'];
				$temp_data['state'] = $state[$row['state']];
				$temp_data['dstate'] = $dstate[$row['dstate']];
				$temp[] = $temp_data;
			}
				
		}
		$responce['page'] = $pageinfo['curpage'];
		$responce['total'] = $pageinfo['total_page'];
		$responce['records'] = $count;
		$responce['rows'] = $temp;
		echo json_encode($responce);
	}

	public function ajaxGetAppList() {
		if( !is_post() || !is_ajax_call() ) exit('__HALT__');
		$post = html_encode($_POST);
		$table = $post['table'];
		$page = to_int($_REQUEST['page']);
		$limit = to_int($_REQUEST['rows']);
		$sidx = strtolower($_REQUEST['sidx']);
		$sord = strtolower($_REQUEST['sord']);
		$sord = !in_array($sord, array('desc','asc'))?'desc':$sord;
		$count = M('')->field('ma.uid, ma.createtime, dt.text')
					  ->table('monitor_app ma')
					  ->join('source s ON s.id = ma.sourceid')
					  ->join('dict dt ON dt.textid = s.textid')
					  ->count();
		$pageinfo = parse_page($count, $limit, $page);
		if ($count > 0) {
			$total_pages = ceil($count / $limit);
		} else {
			$total_pages = 0;
		}
		if ( $count > 0 ) {
			$start = $pageinfo['limit']['start'];
			$end = $pageinfo['limit']['end'];
			$result = M('')->field('ma.uid, ma.createtime, s.location, dt.text')
					  ->table('monitor_app ma')
					  ->join('source s ON s.id = ma.sourceid')
					  ->join('dict dt ON dt.textid = s.textid')
					  ->order("{$sidx} {$sord}")->limit("{$start},{$end}")->select();
			foreach ($result as $k=>$row) {
				$temp_data['id'] = $k;
				$temp_data['uid'] = $row['uid'];
				$temp_data['createtime'] = date('Y-m-d H:i:s',$row['createtime']);
				$temp_data['text'] = $row['location'] . '区-' . $row['text'];
				$temp[] = $temp_data;
			}
	
		}
		$responce['page'] = $pageinfo['curpage'];
		$responce['total'] = $pageinfo['total_page'];
		$responce['records'] = $count;
		$responce['rows'] = $temp;
		echo json_encode($responce);
	}
	
	public function oper() {
		if(!is_post() || !is_ajax_call()) exit('__HALT__');
		$post = html_encode($_REQUEST);
		$table = $post['tab'];
		$oper = $post['oper'];
		$fields = M($table)->getDbFields();
		$pk = $fields[0];
		$data = array();
		$time = time();
		foreach($post as $k=>$v) {
			if(in_array($k, $fields) && $v !='' && $k!=$pk) {
				$data[$k] = $v;
			}
		}
		$id = $post['id'];
		switch ($oper) {
			case 'add':
				if (in_array('timestamp',$fields)) $data['timestamp'] = $time;
				if (in_array('createtime',$fields)) $data['createtime'] = $time;
				if (in_array('modifytime',$fields)) $data['modifytime'] = $time;
				$ok = M($table)->add($data);
				break;
			case 'edit':
				if (in_array('timestamp',$fields)) $data['timestamp'] = $time;
				if (in_array('modifytime',$fields)) $data['modifytime'] = $time;
				$ok = M($table)->where(array($pk=>$id))->save($data);
				break;
			case 'del':break;
			case 'search':break;
		};
		echo $ok;
	}
	
}

