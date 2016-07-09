<?php
import('@.Action.Base');
class IndexAction extends BaseAction {
	function _initialize(){
		parent::_initialize();
	}
	function index(){
		return _to_login_page();
	}
    public function logout() {
		return _destory_login_status();
    }

	public function object2array($object)
	{
		return json_decode(json_encode($object), true);
	}

}