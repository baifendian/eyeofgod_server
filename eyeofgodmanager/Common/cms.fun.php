<?php 
/*
 *根据条件查询合作伙伴或者焦点图的列表信息
 * $type   属于合作伙伴还是焦点图(link：合作伙伴    focus焦点图)
 * $limit  如果是空，则全部数据
 * 排序按照sort字段，asc排序
 * return $result_widget  数组
 */
function get_cms_widget_list($type,$limit=''){
    $limit = to_int($limit);
    $limit = empty ($limit)?"":" limit 0,".$limit;
    $sql = "select title,url,thumb,group_id,remark,sort from ##__cms_widget where status in (1) and type = '$type' order by sort asc $limit";
    return D()->query($sql);
}

/*
 * 获得网站基本信息
 */
function _get_site_config($arg = ''){
    $sql = "select * from ##__system_config where `key` = '__site_config__'";
    $config_content = D()->query($sql);
    $temp = array();
    foreach($config_content as $v){
        $temp[$v['key']] =  unserialize_deep($v['config_content']);
    }
    return $temp;
}
/**
 * 获取系统配置
 * @param array $key
 * @Author feng.yin@baifendian.com
 */
function get_site_config($key = ''){
    $data = cache_load('site_config', '_get_site_config');
    return (array)(empty ($key)?$data:$data[$key]);
}
function remove_cache_site_config(){
    $cache_string = "site_config";
    return cache_remove($cache_string);
}
function get_cms_content($special_sign){
	if(empty($special_sign))return array();
	$sql = "SELECT * FROM `##__cms_contents` WHERE status in (1) and `special_sign` = '$special_sign' order by id desc LIMIT 0,1";
	return (array)current(D()->query($sql));
}

function get_cms_systemannouncement($category_name,$num = ""){
	if(empty($category_name))return array();
	$sql = "SELECT * FROM `##__cms_contents` AS con LEFT JOIN `##__cms_category` AS cate ON con.`category_id` = cate.`category_id` 
	WHERE con.`status` in (1) and cate.`category_name` = '$category_name' ORDER BY id DESC" . " $num";
	return (array)(D()->query($sql));
}
function get_category_content(){
	
}

?>