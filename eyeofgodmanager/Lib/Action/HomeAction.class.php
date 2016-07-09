<?php
import('@.Action.Base');
error_reporting(E_ALL);

class HomeAction extends BaseAction {
	public $is_open_sms_verify = true;
	public function _initialize(){
		parent::_initialize();
	}
	public function index() {
		js_location('Dict/index');
		return;
		$this->assign(array(
			'title' => '上帝之眼',
			'dashboard'=>true,
			'__curent_model__' => $this->_curent_model
		));
		$this->display('default.html');
	}



}

