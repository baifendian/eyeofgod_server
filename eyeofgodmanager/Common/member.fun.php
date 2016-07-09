<?php

/**
 * 构造管理员密码
 * @param string $uname
 * @param string $pass
 * @param int $uid
 * @Author feng.yin@baifendian.com
 */
function get_admin_pass($uname, $pass, $uid) {
    return md5($uname . md5($pass . $uid) . $pass);
}

function re_hash_user_pass($has_pass, $uid) {
    return md5($uid . $has_pass);
}
function get_member_verify_log($uid){
	if(empty($uid))return array();
	return D()->query("select * from `##__member_verify_log` where `uid` = '$uid'  order by `time` desc ");
}
function _get_user_rehash_pass() {
    return $_SESSION[C('USER_AUTH_KEY')]['__new_pass__'];
}

function get_admin_session_key() {
    return C('USER_AUTH_KEY');
}

/**
 * 获取当前用户UID
 * @return return int
 * @author feng.yin
 * @date 2012-6-18下午03:38:52
 * @version V1R6B005
 */
function get_uid($_trueid = false) {
    $uid =  $_SESSION[C('USER_AUTH_KEY')]['uid'];
    /**观察者**/
    if ( !$_trueid ) {
    	if ( $_SESSION[C('USER_AUTH_KEY')]['level'] == 2 ) {
    		$uid = $_SESSION[C('USER_AUTH_KEY')]['creator'];
    	}
    }
    /**观察者**/
    return empty($uid)?0:(int)$uid;
}

/**
 * 获取当前用户的角色ID
 * @return return int
 * @author feng.yin
 * @date 2012-6-18下午03:38:48
 * @version V1R6B005
 */
function get_role_id() {
    return (int) $_SESSION['role_id'];
}

/**
 * 重新设置管理员的角色
 * @Author feng.yin@baifendian.com
 */
function reset_role_id($config) {
    if (empty($config))
        return false;
    $old = (array) $_SESSION[C('USER_AUTH_KEY')];
    $new_set = array_merge($old, $config);
    $_SESSION[C('USER_AUTH_KEY')] = $new_set;
    $_SESSION['rolename'] = $config['rolename'];
    $_SESSION['roleid'] = $config['roleid'];
    $_SESSION['admin_role_name'] = $config['rolename'];
    $_SESSION['admin_role_id'] = $config['roleid'];
}

/**
 * 清除session 
 * @Author feng.yin@baifendian.com
 */
function _destory_login_status($return = true) {
    @session_destroy();
    if ($return) {
        return _to_login_page();
    }
}

/**
 * 判断当前登录状态 
 * @return return boolean
 * @author feng.yin
 * @date 2012-6-18下午03:39:20
 * @version V1R6B005
 */
function _get_login_status($supper = false) {
    $uid = get_uid();
    $u = get_user();
    return !empty($u) && !empty($uid) ? true : false;
}

function _to_login_page() {
    return js_location("/");
}

function _to_default_page($return = false) {
    $url = '/Common/mainIndex/?rand=' . mt_rand();
    if ($return)
        return $url;
    return js_location($url);
}

/* * ***************************用户角色判断****************************************** */

/**
 * 获取当前管理员标志
 * @return return_type
 * @author feng.yin
 * @date 2012-6-18下午03:39:42
 * @version V1R6B005
 */
function get_admin_flag() {
	//当前管理员权限标志
    //$flag = get_admin_flag_sign();
    $role_sign = get_admin_flag_sign();
    return  $role_sign;
}
/**
 * 获取当前管理员类型标记
 * @throws Exception
 * @author feng.yin@baifendian.com
 * @date 2012-8-19
 */
function get_admin_flag_sign($supper) {
	return trim(strtolower($_SESSION['__admin_role_data__']['admin_role_flag']));
}

/**
 * 是否是超级管理员（创始人）拥有所有权限
 * $author feng.yin@1365957639
 */
function is_origin_admin() {
    $save_data = $_SESSION[C('USER_AUTH_KEY')];
    $type = $save_data['type'];
    $__supper_admin_user__ = $save_data['__supper_admin_user__'];
    return $__supper_admin_user__ == '1' && $type == '1' && empty($save_data['role_id'])  && empty($save_data['__admin_role_data__']) ? true : false;
}
/**
 * 判断是否是财务人员
 */
function is_caiwu(){
	return get_admin_flag()=='caiwu' && get_role_id() >0 && get_uid()>0?true:false;
}
/**
 * 判断是否是后台管理员
 * @return boolean
 * @author feng.yin@1365957639
 */
function is_admin() {
	$arg = $_SESSION['super_admin_flag'];
	$user_admin_type = $_SESSION['user_type'];
	return ($arg=='2') || is_origin_admin() ? true : false;
   // return get_admin_flag() == 'admin' ? true : false;
}

/**
 * 判读是否是代理商
 * @Author feng.yin@baifendian.com
 */
function is_agents() {
	return get_admin_flag() == 'agent' && get_role_id() >0 && get_uid()>0 ? true : false;
}

/**
 * 计算用户资金
 */
function calculation_user_money($uid){
	$db = D();
	if(empty($uid))return 0;
	$sql = "SELECT SUM(l.`money`) as total_money FROM `atd_member_money_log` AS l 
	 WHERE l.`uid` = '$uid' AND l.`status` = '1' and ``; ";	
	$data = $db->query($sql,true);
	return $data['total_money'];
}

/**
 * 判断是否是广告主
 */
function is_advertiser($all = true) {
	//if(is_special_appkfc() && $all)return true;
    return in_array(get_admin_flag(), array('advertiser','advertiser_view')) && get_role_id() >0 && get_uid()>0 ? true : false;
    //return get_admin_flag() == 'advertiser' && get_role_id() >0 && get_uid()>0 ? true : false;
}

/**
 * 判断是否是广告主的观察者
 */
function is_advertiser_view() {
	return get_admin_flag() == 'advertiser_view' && get_role_id() >0 && get_uid()>0 ? true : false;
}
/**
 * 获取当前用户的基础信息 
 * @return multitype:unknown 
 * Author feng.yin@baifendian.com
 * Date 2013-4-26
 */
function _get_curent_member_parent_data() {
    $data = $_ENV['__DB_TEMP_CURENT_USER_DATA__'];
    if (!$data) {
        $auth = D('Auth');
        $auth->verify_member_status();
        $data = $_ENV['__DB_TEMP_CURENT_USER_DATA__'];
    }
    return array(
        'agent_id' => $data['parent_id'],
        'temp_data' => $data,
    );
}
/**
 * 根据代理商ID 获取对应的广告主
 * @param int $agents_id
 * @Author feng.yin@baifendian.com
 */
function get_agents_advertiser($agents_id = '', $check = true) {
    if (empty($agents_id) && is_agents()) {
        $agents_id = get_uid();
    }
    if (empty($agents_id))
        return array();
    $check_string = '';
    if ($check) {
        $check_string = " AND `status` = '1' ";
    }
    return D('members')->field('uid,email,IF(LENGTH(`username`)>0,`username`,`user_alias_name`) AS username')
    ->where("`parent_id` = '$agents_id' AND `type` in (3,11) $check_string ")
    ->order("username desc")->select();
}
/**
 * 根据代理商ID 获取当前账户下的的广告主
 * @param boolean $check
 * @Author feng.yin@baifendian.com
 */
function get_current_advertiser($check = true) {
	$uid = get_uid();
	$where = '1=1';
	if ( is_agents() ) $where .= ' AND parent_id=' . $uid;
	if ( is_advertiser() ) $where .= ' AND uid=' . $uid;
	if ( $check ) $where .= ' AND `status` = "1" AND `status_oper` = "1" AND `type` = "3" ';
	$advertiser = D('members')->field('`uid`, `email`,`username`,`user_alias_name`, `parent_id`, `usable_money`, `status`,`type`')->where($where)->order("username desc")->select();
	return array_group_by( $advertiser, 'uid', false );
}
function get_advertiser_agent($uid){
	$sql = "select `parent_id` from `##__members` where `uid` = '$uid' limit 0,1 ";
	$data = D()->query($sql,true);
	return $data['parent_id'];
}
function get_current_member_session_data() {
    return $_SESSION[C('USER_AUTH_KEY')];
}

function get_user() {
    return $_SESSION[C('USER_AUTH_KEY')]['username'];
}
function get_current_admin_user_name(){
	return get_user();
}

function _get_admin_id() {
    return get_uid();
}

//根据用户的ID获取用户的信息
function get_memberinfo($id) {
    $memberinfo = M('members')->field('IF(LENGTH(`user_alias_name`)>0,`user_alias_name`,`username`) AS username')->where("uid=$id")->order("username desc")->find();
    return $memberinfo;
}

//-----------------------------------------feng.yin-------移植从会员专刊项目中的member.fun.php中移植过来的代码
/**
 * 判断是否是客户人员
 * @return boolean
 * Author feng.yin@baifendian.com
 * Date 2013-5-23
 */
function is_client_user() {
    return get_admin_flag() == 'kh' ? true : false;
}
/**
 * 是否是客服管理员
 * @return boolean
 * Author feng.yin@baifendian.com
 * Date 2013-5-23
 */
function is_customer_manager() {
    return get_admin_flag() == 'kf_admin' ? true : false;
}

/**
 * 判读是否是客服人员
 * @return boolean 
 * Author feng.yin@baifendian.com
 * Date 2013-5-23
 */
function is_customer() {
    return get_admin_flag() == 'kf' ? true : false;
}

/**
 * 判断是否是运营人员
 * @return boolean
 * Author feng.yin@baifendian.com
 * Date 2013-5-23
 */
function is_operations_manager() {
    return get_admin_flag() == 'admin' && !is_origin_admin() ? true : false;
}

/**
 * 获取所有系统中的用户
 * @param string|int $id
 * @param string 
 * @param boolean
 * Author feng.yin@baifendian.com
 * Date 2013-5-27
 */
function get_user_data($uid = '', $member_type = 'kh', $check_remote_code = true, $check_user_status = false) {
    $sql = "SELECT 
    m.`uid`,
    m.`username`,
    m.`status`,
    m.`roleid`,
    m.`password`,
    m.`email`,
    m.`mobile`,
    m.`parent_id`,
    m.`super_admin_flag`,
    r.`name` AS rolename,
    r.`flag`,
    r.`id` AS rid
FROM
    `##__members` AS m 
    LEFT JOIN ##__member_role AS r 
        ON m.`roleid` = r.`id`
WHERE r.`status` = '1'  ";
    if ($check_user_status) {
        $sql.=" AND m.`status` = '1' ";
    }
//    if ($check_remote_code) {
//        $sql.=" and m.`remote_code_status` = '1' ";
//    }
    $admin_ary = array(
        'admin' => '1', //管理员 运营人员
        'kf_admin' => '2', //广告主
        'kf' => '3', //客服
        'kh' => '4', //客户
    );
    $find = $admin_ary[$member_type];
    if ($find) {
        $sql.=" AND r.`id` = '$find' ";
    }
    $id = get_array_value_for_int($uid, '', true);
    if ($id) {
        $sql.=" AND m.`uid` IN($id) ";
    }
    $sql.=" ORDER BY m.`uid` ASC ";
    return D()->query($sql);
}
/**
 * 获取当前周一时间
 * 
 * @return 
 */
function getCurrdate() {
    $nowweek = date('w');
    switch ($nowweek) {
        case 1:
            $mondate = date('Y-m-d', strtotime("+7 day"));
            break;
        case 2:
            $mondate = date('Y-m-d', strtotime("+6 day"));
            break;
        case 3:
            $mondate = date('Y-m-d', strtotime("+5 day"));
            break;
        case 4:
            $mondate = date('Y-m-d', strtotime("+4 day"));
            break;
        case 5:
            $mondate = date('Y-m-d', strtotime("+3 day"));
            break;
        case 6:
            $mondate = date('Y-m-d', strtotime("+2 day"));
            break;
        case 0:
            $mondate = date('Y-m-d', strtotime("+1 day"));
            break;
    }

    return $mondate;
}

?>