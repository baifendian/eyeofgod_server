<?php
import('@.Action.Base');

error_reporting(E_ALL);

class DictAction extends BaseAction {
	public function _initialize(){
		parent::_initialize();
	}
	function index() {
		$_tables = array(
			'source'=>'资源明细',
			//'event'=>'事件',
			//'dict'=>'资源字典',
			//'user'=>'用户',
			'device'=>'设备列表',
		);
		$_ignoreField = array(
			'user'=>array('token','advanced'),
		);
		if ( $_GET['tab'] ) {
			$table = $_GET['tab'];
		} else {
			$table = key($_tables);
		}
		$temp_fields = M($table)->getDbFields();
		
		$temp_fields_des =  M($table)->query("SHOW FULL FIELDS FROM $table");
		
		$map_fields_des = array();
		foreach ($temp_fields_des as $k=>$v) {
			$map_fields_des[$v['Field']] = $v['Comment'];
		}
		$i = 0;
		foreach($temp_fields as $k=>$v) {
			if ( !in_array($v, $_ignoreField[$table]) ) {
				if ($k !== '_autoinc' && $k !== '_pk') {
					$fields[$i]['name'] = $v;
					$fields[$i]['comment'] = $map_fields_des[$v];
					$i++;
				}
			}
		}
		$select_config = array(
			'location'=>array(
				array("id"=>'A',"name"=>'A'),
				array("id"=>'B',"name"=>'B'),
				array("id"=>'C',"name"=>'C'),
				array("id"=>'D',"name"=>'D'),
				array("id"=>'E',"name"=>'E'),
				array("id"=>'F',"name"=>'F')
			),
			'textid'=>array_group_by(M('dict')->field('textid as id,text as name')->select(), 'id', false)
		);
		$this->assign(array(
			'title'=>"资源管理",
			'__curent_model__' => $this->_curent_model,
			'list' => true,
			'pk'=>M($table)->getPk(),
			'select_config'=>$select_config,
			'fileds'=>$fields,
			'alltables'=>$_tables,
			'table'=>$table
		));
		$this->display('Dict/index.html');
	}
	
	public function ajaxGetList() {
		$state = array(array("name"=>"空闲"),array("name"=>"占用"));
		$sex = array(array("name"=>"女"),array("name"=>"男"));
		$sourceType = array_group_by(M('dict')->field('textid as id,text as name')->select(), 'id', false);
		
		$config = array(
			'state'=>$state,
			'sourceType'=>$sourceType,
		);
		$maping = array(
			'textid'=>'sourceType',
			'state'=>'state',
		);
		if( !is_post() || !is_ajax_call() ) exit('__HALT__');
		$post = html_encode($_POST);
		$table = $post['table'];
		$page = to_int($_REQUEST['page']);
		$limit = to_int($_REQUEST['rows']);
		$sidx = strtolower($_REQUEST['sidx']);
		$sord = strtolower($_REQUEST['sord']);
		$sord = !in_array($sord, array('desc','asc'))?'desc':$sord;
		$where = $this->_where[$table] ? $this->_where[$table] : array();
		$count = M($table)->where($where)->count();
		$pageinfo = parse_page($count, $limit, $page);
		if ($count > 0) {
			$total_pages = ceil($count / $limit);
		} else {
			$total_pages = 0;
		}
		if ( $count > 0 ) {
			$pk = M($table)->getPk();
			$start = $pageinfo['limit']['start'];
			$end = $pageinfo['limit']['end'];
			$result = M($table)->where($where)->group($pk)->order("{$sidx} {$sord}")->limit("{$start},{$end}")->select();
			foreach ($result as $row) {
				foreach($row as $k=>$v) {
					$temp_data[$k] = in_array($k, array('createtime','modifytime','timestamp')) ? date('Y-m-d H:i:s',$v) : $v;
					if ($maping[$k] && $k!=$pk) {
						$temp_data[$k] = $config[$maping[$k]][$v]['name'];
					}
				}
				$temp_data['id'] = $temp_data[$pk];
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
			case 'del':
				$ok = M($table)->where(array($pk=>$id))->delete();
				break;
			case 'search':break;
		};
		echo $ok;
	}
	
	/***********END*************/

}

