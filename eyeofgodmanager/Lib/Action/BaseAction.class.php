<?php
@ob_start();
/**
 * jqgrid数据翻译处理函数
 * @author feng.yin@baifendian.com
 * @date 2015-9-20
 */
function __cover_ajax_data__()
{
    $data = trim(ob_get_contents());
    @ob_end_clean();
    $decode_data = json_decode($data, true);
    if ($decode_data) {
        echo json_encode(__replace_lang($decode_data)); exit();
    }
    echo $data;
    exit();
}

/**
 * 替换语言包输出数据转化   递归处理
 * @param string|array $replace_data
 * @author feng.yin@baifendian.com
 * @date 2015-9-20
 */
function __replace_lang($replace_data)
{
    $replace_setting = $_ENV['__lang_replace_data__'];
    if (empty($replace_setting) || !is_array($replace_setting))
        return $replace_data;
    if (is_array($replace_data)) {
        foreach ($replace_data as &$temp)
            $temp = __replace_lang($temp);
    } else {
        $replace_data = strtr($replace_data, $replace_setting);
    }
    return $replace_data;
}

/**
 * 核心基础类
 * @package        action
 * @author          feng.yin <feng.yin@baifendian.com>
 * @copyright       Copyright (c) 	2011
 * @version         $id: V0.6
 */
class BaseAction extends Action
{
	protected $salt = 'nmas790R67Imlasdfsadd9a39c1840145312cb1738a29fa998514aab7d930a1cf3423423yuasnk045';
    private $_lang_replace_data = array();
    protected $_tpl = '';
    private $_is_need_cover_lang = false;
    protected $uid = null;
    protected $model = null;
    protected $moduleName = null;
    protected $_curent_model = '';
    protected $_curent_action = '';
    
    function _initialize(){
    	$this->curent_meber_info = $_ENV['__DB_TEMP_CURENT_USER_DATA__'];
    	$this->_curent_action = $curent_action_name = strtolower(ACTION_NAME);
    	$this->_curent_model = $curent_model_name = strtolower(MODULE_NAME);
    	$this->imagepath = C("__PUBLIC__"); //  '/Public'
    	$this->moduleName = $this->getActionName();
    	$this->uid = get_uid();
    	//$this->model = D($this->moduleName);
    	$this->__init_tpl()->_set_base()->_init_lang()->_init_ajax_call();
    }
    
    function query($sql,$one = false,$object = false){
    	return D()->query($sql,$one,$object);
    }

    function _encode($plaintext) {
    	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->salt, $plaintext, MCRYPT_MODE_ECB));
    }
    
    function _decode($encode_string) {
    	return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->salt, base64_decode($encode_string), MCRYPT_MODE_ECB);
    }
    
    /**
     * 捕获ajax输出 截留AJAX 的列表数据输出
     * @author feng.yin@baifendian.com
     * @date 2015-9-20
     */
    function _init_ajax_call()
    {
        $sord = $_REQUEST['sord'];
        $sidx = $_REQUEST['sidx'];
        if (is_ajax_call() && $this->_is_need_cover_lang && $sord && $sidx && $_REQUEST['rows']) {
            register_shutdown_function('__cover_ajax_data__');
        }
        return $this;
    }
    
    function __set($name, $value)
    {
        return $this->$name = $value;
    }
    
    protected function display($file, $cache_id = ''){
    	//if(in_array(strtolower($this->moduleName), array('common','public','home')) || is_ajax_call()) {
    	if(in_array(strtolower($this->moduleName), array('public')) || is_ajax_call()) {
    		return $this->view($file,$cache_id);
    		exit();
    	}
    	$this->assign(array(
    		'right_pannel_data'=>$this->fetch($file),
    	));
    	$this->view('mainindex.html');
    }
    
    function view($file, $cache_id=''){
    	return $this->_tpl->display($file, $cache_id);
    }
    
    public function fetch($file)
    {
        return $this->_tpl->fetch($file);
    }
    
    public function assign($key, $value = '')
    {
        return $this->_tpl->assign($key, $value);
    }
    
    /**
     * 获取数据库的字典配置
     * @return multitype:
     * @author feng.yin@baifendian.com
     * @date 2015-9-20
     */
    function __load_db_table_lang()
    {
        $replace_table_data = array();
        $lang_file          = SITE_ROOT . 'Lang/db_table.php';
        if (is_file($lang_file)) {
            $replace_table_data = array_merge($replace_table_data, (array) include $lang_file);
        }
        return $replace_table_data;
    }
    
    private function __init_tpl()
    {
        $lang = strtolower(C('DEFAULT_LANG'));
        //设置语言变量到模版
        C('TMPL_PARSE_STRING.__LANG__', $lang);
        if (!is_object($this->_tpl)) {
            import('@.ORG.template');
            $this->_tpl                       = new template();
            $this->_tpl->error_reporting      = C('TPL_DEBUG');
            $this->_tpl->template_dir         = C('TPL_MAIN_PATH');
            $this->_tpl->template_name        = C('DEFAULT_THEME');
            $this->_tpl->template_compile_dir = C('TPL_TEMP_PATH') . 'cached/';
            $this->_tpl->template_cache_dir   = C('TPL_TEMP_PATH') . 'cache/';
            $this->_lang_replace_data         = C('TMPL_PARSE_STRING');
            /*处理模版文件多语言替换逻辑*/
            if (!in_array($lang, array(
                'zh-cn',
                'zn_cn'
            ))) {
                $this->_is_need_cover_lang = true;
                $this->_lang_replace_data  = array_merge(cache_load('db_table_lang', array(
                    $this,
                    '__load_db_table_lang'
                )), $this->_lang_replace_data);
            }
            $_ENV['__lang_replace_data__'] = $this->_lang_replace_data;
            $this->_tpl->replace_key       = $this->_lang_replace_data;
            if (C('TPL_DEVELOP_TYPE')) {
                $this->_tpl->replace_space = false;
                $this->_tpl->develop_type  = true;
            }
            /*
            $lang_file = SITE_ROOT.'Lang/'.C('DEFAULT_LANG').'/'.strtolower(MODULE_NAME).'.php';
            $lang_fileb = SITE_ROOT.'Lang/'.C('DEFAULT_LANG').'/'.strtolower('common').'.php';
            $a = array();
            if(is_file($lang_file)){
            $a = include $lang_file;	
            }
            $b = include $lang_fileb;
            $lang_data = array_merge($b,$a);
            */
            $this->assign(array(
                '__LANG__' => C('DEFAULT_LANG')
                /*'Think'=>array(
                'lang'=>$lang_data,
                )*/
            ));
        }
        
        return $this;
    }
    
    function _init_lang()
    {
        _init_lang();
        return $this;
    }
    
    function tpl_debug()
    {
        return $this->_tpl->debug();
    }
    
    private function _set_base() {
    	return $this;
    }
    
    /*
    private function _set_base()
    {
        $this->adbfp_sys_config = $result_config = get_site_config();
        //注册标量到内存
        //C('amp_short_link', $result_config['adbfp_site_config']['amp_short_link']);
        $this->assign(array(
            'title' => $this->adbfp_sys_config['__site_config__']['title'],
            '__sysytem_config__' => $result_config,
        	'bmm_version'=>C('BMM_VERSION').C('CSS_JS_LAST_UPDATE_TIME'),
        ));
        return $this;
    }
    */
    //取得完整的默认报表数组，以进行更改后重新存入数据库
    function get_default_array($model)
    {
        if (empty($model))
            return 0;
        $uid  = get_uid();
        $key  = "user_all_self_config_" . $uid;
        $data = cache_load($key, array(
            $this,
            'get_settings'
        ), array(
            $uid
        ));
        return empty($data) ? 0 : $data;
    }
}
?>