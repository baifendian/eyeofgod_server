<?php 
/**
 * 替换空白回调函数
 * @param array $match
 */
function __call_back_temp_fun__($match){
	$GLOBALS['script_replace_temp_array'][] = $match[1];
	static $d_tag = 0;
	$e_string = '___lazy_frame_temp_string___'.$d_tag.'___lazy_frame_temp_string___';
	$GLOBALS['script_replace_temp_string'][]= $e_string;
	$d_tag++;
	return $e_string;
}
/**
 * 替换空白
 * @param string $data 要替换的数据
 * @param boolean $no_include_script 不替换JS的空白 默认 不替换
 */
function replace_space($data,$no_include_script = true){
	$a = array(
			"/\\s(?=\\s)/i",
			"/[\n\r\t]/",
	);
	$b = array(
			'',
			'',
	);
	if(!$no_include_script) return preg_replace($a,$b,$data);
	$sa = array(
			"/<script([\\s\\S]*?)>([\\s\\S]*?)<\\/script>/i"
	);
	$sb = array(
			'___lazy_frame_temp_string___\\0___lazy_frame_temp_string___'
	);
	$GLOBALS['script_replace_temp_array'] = array();
	$GLOBALS['script_replace_temp_string'] = array();
	$end =  preg_replace_callback('/___lazy_frame_temp_string___([\\S\\s]*?)___lazy_frame_temp_string___/i','__call_back_temp_fun__', preg_replace($sa, $sb, $data));
	$result = str_replace($GLOBALS['script_replace_temp_string'], $GLOBALS['script_replace_temp_array'], preg_replace($a,$b,$end));
	unset($GLOBALS['script_replace_temp_array'],$GLOBALS['script_replace_temp_string'],$sa,$sb);
	return trim($result);
}
function __call_back_temp_form_fun__($match){
	static $d_tag = 0;
	$hash = get_uique_id();
	$str = '<input type="hidden" value="'.$hash.'"  name="___lazy_hash_key___"/></form>';
	$e_string = str_replace('</form>', $str, $match[1]);
	$d_tag++;
	$GLOBALS['script_replace_temp_form_string_session_key'][$hash] = $hash;
	return $e_string;
}
/**
 * 给表单加上字符串
 * @param unknown_type $data
 */
function add_form_hash($data){
	if(empty($data))return $data;
	$rule = array(
			"/<form([\\s\\S]*?)>([\\s\\S]*?)<\\/form>/i"
	);
	$sb = array(
			'___lazy_form_temp_string___\\0___lazy_form_temp_string___'
	);
	$GLOBALS['script_replace_temp_form_string_session_key'] = array();
	$end = preg_replace_callback('/___lazy_form_temp_string___([\\S\\s]*?)___lazy_form_temp_string___/i','__call_back_temp_form_fun__', preg_replace($rule, $sb, $data));
	return array(
			'string'=>$end,
			'hash'=>$GLOBALS['script_replace_temp_form_string_session_key'],
	);
}
/**
 * 替换所有JS 和frame代码
 * @param $string
 */
function replace_script($string){
	if(empty($string))return $string;
	return  preg_replace(array(
			"/<script([\\S\\s]*?)script>/is",
			"/<iframe([\\S\\s]*?)iframe>/is"
	),array('',''),strtolower($string));
}

?>