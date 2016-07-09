<?php
/**
 * ==============================================================================================================
 * $version: 1.0
 * $time: 20110501
 * $author:bizman
 * $QQ:1365957639
 * ==============================================================================================================
 */
class template
{
    public $template_dir = 'templates';
    /*模板目录*/
    public $template_dir_old = 'templates';
    public $template_cache_dir = 'templates_cache';
    /*模板缓存目录*/
    public $template_compile_dir = 'templates_compile';
    /*模板编译文件夹*/
    public $replace_key = array();
    public $cache_lifetime = 3600;
    /*缓存周期*/
    public $error_reporting = false;
    public $is_public = true;
    /*那个模板*/
    public $flush = false;
    /*是否 强制边编译边输出 默认开启*/
    public $_foreachmark = '';
    public $return = false;
    public $template_name = 'default';
    /*当前系统使用的模板*/
    protected $_cache_id = '';
    /*缓存临时文件*/
    protected $_foreach = array();
    protected $_tpl_var = array();
    protected $_tpl_vars = array();
    protected $_filter = array();
    protected $_cache_file = '';
    /*当前读取的缓存文件*/
    private $_temp_key = array();
    private $_temp_val = array();
    private $_plugins = array();
    private static $_log = array();
    private static $_instance = __CLASS__;
    public $charset = 'utf-8';
    public $replace_space = true;
    /*删除空白*/
    public $develop_type = false;
    /*是否开发模式*/
    private $_ace = '__BFP_CORE__';
    /*扩展读取父模板内容*/
    private $_app = '__BFP_CORE__';
    public $model = '';
    public $close_filter = false;
    public static function getInstance()
    {
        return is_object(self::$_instance) ? self::$_instance : new self::$_instance;
    }
    function __construct()
    {
        $this->_set_error();
        _set_header();
    }
    function __destruct()
    {
        unset($this);
    }
    function log()
    {
        return self::$_log;
    }
    /**
     * 模板 赋值
     * @param $tpl_var
     * @param $value
     */
    function assign($tpl_var, $value = '')
    {
        if (empty($tpl_var))
            return '';
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $key => $val)
                $key != '' ? $this->_tpl_var[$key] = $val : '';
        } else {
            $this->_tpl_var[$tpl_var] = $value;
        }
        unset($tpl_var);
    }
    /**
     * 直接显示 直接执行eval  无返回结果
     * @param $filename 
     * @param $cache_id
     * @return void
     */
    function display($filename, $cache_id = '')
    {
        if (!empty($cache_id) && empty($this->_cache_id)) {
            $this->_cache_id = $cache_id;
            $this->flush     = false;
        }
        echo $this->_fetch($filename);
    }
    public function fetch($filename)
    {
        return $this->_fetch($filename, true);
    }
    function flush()
    {
        @ob_start();
        @ob_flush();
        @flush();
    }
    /**
     * 返回OB_FLUSH刷新代码
     */
    protected function _get_flush()
    {
        return $this->flush ? '<?php @ob_flush();@flush(); ?>' : '';
    }
    /**
     * @param $filename
     * @param $return 是否返回 默认返回  若返回速度会慢一些 会抓取输出缓冲 若不返回者直接执行了
     */
    protected function _fetch($filename, $return = true)
    {
        $time1 = microtime(true);
        $this->_set_error();
        $filename = $this->_check_tpl_file($filename);
        /*执行编译并返回结果 此模式会检查编译时间周期*/
        $out      = $this->_make_compiled($filename, $return);
        $this->_set_log($filename, $time1);
        return $out;
    }
    protected function _set_log($filename, $time1, $extend_flag = '')
    {
        $time2        = microtime(true);
        $log          = array(
            'file' => $filename,
            'start' => $time1,
            'end' => $time2,
            'diff' => number_format($time2 - $time1, 6)
        );
        self::$_log[] = $log;
        unset($log);
    }
    protected function _do_replace_data($data)
    {
        return $this->replace_space ? replace_space($data) : $data;
    }
    /**
     * 注册插件 没有处理处理过滤
     * @param string $key
     * @param string $value
     * @return void
     */
    function register_function($key, $value)
    {
        $this->_plugins[$key] = $value;
    }
    /**
     * 加载过滤器
     * @param string $sign 过滤器类型
     * @param string $function 当前过滤器的方法 (局部)
     */
    function load_filter($sign, $function)
    {
        $this->_filter[$sign] = $function;
    }
    function debug()
    {
        $result   = '';
        $logs     = $this->log();
        $all_file = array();
        $all_time = array();
        $process  = '';
        if (!empty($logs)) {
            foreach ($logs as $key => $item) {
                $diff       = $item['diff'];
                $all_file[] = $item['file'];
                $all_time[] = $diff;
                $process .= ($key + 1) . '、' . $item['file'] . '  =>' . $diff . '<br />';
            }
            $result .= '<fieldset class="lazy_debug_pannel">';
            $result .= '<legend>模板编译文件清单,共耗时<span class="lazy_time_set">' . array_sum($all_time) . 'ms</span></legend>';
            $result .= '<p class="lazy_tpl_load_file">' . $process . '</p>';
            $result .= '</fieldset>';
        }
        return $result;
    }
    /**
     * 前台公用调用 检查是否过期 过期返回true 没过期返回false
     * @param $file
     * @param $cache_id
     */
    function check_cache($display, $cache_id, $life_time = 3600)
    {
        return $this->_is_expired($this->_set_cache_file($display, $cache_id), $life_time);
    }
    /**
     * 加载静态缓存文件 执行结束后会退出
     * @param $filename
     * @param $cache_id
     */
    function load_cache($display, $cache_id, $life_time = 0)
    {
        if (!$this->check_cache($display, $cache_id, $life_time)) {
            echo @file_get_contents($this->_cache_file);
            $this->flush();
            exit();
        }
    }
    /**
     * 直接删除编译后的文件 静态缓存文件
     * @param $filename
     * @param $cache_id
     */
    function remove($filename, $cache_id)
    {
        @unlink($this->_set_cache_file($filename, $cache_id));
    }
    function _error($error_msg)
    {
        return _exception($error_msg);
    }
    /**
     * 解析模板INCLUDE 文件的调用 若不存在直接使用默认的 默认的还不在直接退出
     * @param string $filename
     */
    private function _check_tpl_file($filename)
    {
        if (!$this->is_public && false !== strpos($filename, $this->_ace)) {
            $this->template_dir = ADMIN_TPL_PATH;
            $filename           = str_replace($this->_ace, '', $filename);
        } elseif (!$this->is_public && false !== strpos($filename, $this->_app)) {
            $this->template_dir = $this->template_dir_old;
            $filename           = str_replace($this->_app, '', $filename);
        }
        $filename = $this->template_dir . (!empty($this->template_name) ? $this->template_name . '/' : '/') . $filename;
        if (!is_file($filename))
            return $this->_error('unable to read tpl resource: "' . $filename . '"');
        return $filename;
    }
    private function _set_error()
    {
        $this->error_reporting ? error_reporting(E_ALL | E_WARNING) : '';
    }
    /**
     * 判断文件是否过期  过期为或文件不存在 或文件大小为0 返回true 否则返回false
     * @param string $file
     */
    private function _is_expired($file, $life_time = 0)
    {
        /*开发模式直接返回过期 开发模式会比较耗费资源*/
        if ($this->develop_type)
            return true;
        if (!@filesize($file))
            return true;
        $stats = @stat($file);
        if (!$stats)
            return true;
        $life_time  = empty($life_time) ? $this->cache_lifetime : $life_time;
        $expir_time = $stats['mtime'] + $life_time;
        return $expir_time < time() ? true : false;
    }
    /**
     * 设置静态缓存文件文件路径
     * @param $filename
     * @param $cache_id
     */
    protected function _set_cache_file($filename, $cache_id = '')
    {
        $cache_id = empty($cache_id) ? $this->_cache_id : $cache_id;
        return $this->_cache_file = $this->template_cache_dir . (!empty($this->template_name) ? $this->template_name . '/' : '/') . substr(md5($cache_id), 0, 2) . '/' . $cache_id . '_' . md5($cache_id) . '.php';
    }
    /**
     * 替换数据
     */
    private function __h()
    {
        return '';
    }
    /**
     * 返回当前编辑的模板文件 绝对路径
     * @param string $filename
     */
    private function _load_compiled_file($filename)
    {
        //(!empty($this->model)?'/'.$this->model.'/':'').
        return $this->template_compile_dir . (!empty($this->template_name) ? $this->template_name . '/' : '/') . array_shift(explode('.', basename($filename))) . '_' . md5($filename) . '.php';
    }
    /**
     * 编译模板函数
     * @access  public
     * @param   string      $filename
     * @return  sring        编译后文件地址
     */
    private function _make_compiled($filename, $return = true)
    {
        $compiled_file = $this->_load_compiled_file($filename);
        /*若编译的文件过期*/
        if ($this->_is_expired($compiled_file)) {
            $file_data = file_get_contents($filename);
            /*强制加头部代码*/
            if ($this->replace_key && is_array($this->replace_key)) {
                //$file_data = str_replace(array_keys($this->replace_key), array_values($this->replace_key), $file_data);
                $file_data = strtr($file_data, $this->replace_key);
            }
            /*php_strip_whitespace*/
            if (C('__add_form_hash__') == true) {
                $source_callback = add_form_hash($this->_fetch_str($file_data));
                $source          = $source_callback['string'];
                if ($source_callback['hash'])
                    $_SESSION['___lazy_hash_form_key___'] = $source_callback['hash'];
                unset($GLOBALS['script_replace_temp_form_string_session_key']);
            } else {
                $source = $this->_fetch_str($file_data);
            }
            /*写编译文件*/
            if (!$this->_create_file($source, $compiled_file))
                return $this->_error('can\'t write:' . $compiled_file);
            $out = $this->_eval($source, $return);
        } else {
            /*若没过期直接读取编译好了的模板文件*/
            $out = $this->_eval(file_get_contents($compiled_file), $return);
        }
        /*替换空白*/
        $out = $this->_do_replace_data($out);
        /*写静态缓存文件  有替换空白   开发模式下不写编译文件*/
        if (!empty($this->_cache_id) && !$this->develop_type) {
            $static_file = $this->_set_cache_file($filename);
            /*强制去掉空白 replace_space*/
            if (!$this->_create_file($out, $static_file))
                return $this->_error('can\'t write static cache file :' . $static_file);
        }
        return $out;
    }
    private function _create_file($data, $file, $type = "wb+")
    {
        return create_file($data, $file, $type);
    }
    /**
     * 处理字符集过滤 过滤插件等数据
     * @param string $source
     */
    private function _smarty_pre_filter_compile($source)
    {
        /*处理UTF8编码字符集头部*/
        if (strpos($source, "\xEF\xBB\xBF") !== FALSE)
            $source = str_replace("\xEF\xBB\xBF", '', $source);
        $filter = array();
        if (!$this->close_filter) {
            $filter = array(
                'outputfilter' => 'phptag'
            );
        }
        /*强制加载过滤插件 过滤代码*/
        $this->_filter = array_merge($this->_filter, $filter);
        if (!empty($this->_filter)) {
            foreach ($this->_filter as $key => $function) {
                $fun = 'smarty_' . $key . '_filter_' . $function;
                if (function_exists($fun))
                    $source = $fun($source);
            }
        }
        return $source;
    }
    function _get_return()
    {
        return $this->_return ? 'true' : 'false';
    }
    /**
     * 处理{}标签
     * @access  public
     * @param   string      $tag
     * @return  sring
     */
    private function _parse_tpl_variable($tag)
    {
        $tag  = stripslashes(trim($tag));
        $math = array();
        if (empty($tag)) {
            return '{}';
            //语言包标记
        } elseif ($tag{0} == '*' && substr($tag, -1) == '*') {
            return '';
        } elseif ($tag{0} == '$' && substr($tag, 0, 2) != '$(') {
            /*处理变量*/
            return '<?php echo ' . $this->_get_val(substr($tag, 1)) . '; ?>';
            //结束 tag
        } elseif ($tag{0} == '/') {
            switch (substr($tag, 1)) {
                case 'if':
                    return '<?php } ?>';
                    break;
                case 'foreach':
                    if ($this->_foreachmark == 'foreachelse') {
                        $output = '<?php }; unset($_from); ?>';
                    } else {
                        array_pop($this->_patchstack);
                        $output = '<?php };' . "\n" . ' }; unset($_from); ?>';
                    }
                    $output .= "<?php \$this->_pop_vars();?>";
                    return $output;
                    break;
                case 'literal':
                    return '';
                case 'strip':
                    return '';
                    break;
                default:
                    return '{' . $tag . '}';
                    break;
            }
        } else {
            $tag_sel = array_shift(explode(' ', $tag));
            $arg_ary = in_array($tag_sel, array(
                'insert_root_css',
                'insert_css',
                'insert_scripts',
                'insert_template_scripts_root',
                'insert_template_scripts'
            )) ? $this->_parse_attrs($tag) : array();
            switch ($tag_sel) {
                case 'if':
                    return $this->_compile_if_tag(substr($tag, 3));
                    break;
                case 'else':
                    return '<?php }else{ ?>';
                    break;
                case 'elseif':
                    return $this->_compile_if_tag(substr($tag, 7), true);
                    break;
                case 'foreachelse':
                    $this->_foreachmark = 'foreachelse';
                    return '<?php };' . "\n" . ' }else{ ?>';
                    break;
                case 'foreach':
                    $this->_foreachmark = 'foreach';
                    if (!isset($this->_patchstack))
                        $this->_patchstack = array();
                    return $this->_compile_foreach_start(substr($tag, 8));
                    break;
                case 'assign':
                    $t   = $this->_parse_attrs(substr($tag, 7), 0);
                    $tmp = $t['value']{0} == '$' ? '$this->assign(\'' . $t['var'] . '\',' . $t['value'] . ');' : '$this->assign(\'' . $t['var'] . '\',\'' . addcslashes($t['value'], "'") . '\');';
                    return '<?php ' . $tmp . ' ?>';
                    break;
                case 'insert_root_css':
                    return $this->_parse_insert_script($arg_ary, 'css', false, true);
                    break;
                case 'insert_css':
                    return $this->_parse_insert_script($arg_ary, 'css', true, true);
                    break;
                case 'insert_scripts':
                    return $this->_parse_insert_script($arg_ary, 'script');
                    break;
                case 'insert_template_scripts_root':
                    return $this->_parse_insert_script($arg_ary, 'script', false, true);
                    break;
                case 'insert_template_scripts':
                    return $this->_parse_insert_script($arg_ary, 'script', true, true);
                    break;
                case 'break':
                    return '<?php break; ?>';
                    break;
                case 'include':
                    $t      = $this->_parse_attrs(substr($tag, 8), 0);
                    $extend = $t['extend'];
                    $e      = $t['file'];
                    unset($t);
                    return false !== strpos($e, '$this') ? $this->_get_flush() . '<?php echo  $this->_fetch(' . "$e" . '); ?>' : $this->_get_flush() . '<?php echo  $this->_fetch(' . "'$e'" . '); ?>';
                    break;
                case 'break':
                    return '<?php break; ?>';
                case 'continue':
                    return '<?php continue; ?>';
                case 'literal':
                    return '';
                    break;
                case 'strip':
                    return '';
                    break;
                default:
                    $str          = '';
                    $regisert_fun = array_keys($this->_plugins);
                    if (in_array($tag_sel, $regisert_fun)) {
                        $arg = $this->_parse_arg_for_plugin($tag);
                        $fun = $this->_plugins[$tag_sel];
                        return '<?php echo ' . $fun . '(' . $this->_parse_array($arg) . '); ?>';
                    }
                    return '{' . $tag . '}';
                    break;
            }
        }
    }
    private function _parse_insert_script($arg, $type = 'css', $check_tpl = false, $http = false)
    {
        if (empty($arg) || !is_array($arg))
            return '';
        $script  = explode(',', trim(str_replace("'", '', $arg['files'])));
        $out_put = '';
        if (is_array($script)) {
            foreach ($script as $s) {
                if ($check_tpl) {
                    $template = check_tpl_exist($this->template_set);
                    $s        = $this->site_set . 'template/' . $template . '/' . $s;
                } else {
                    $s = $http ? $this->site_set . $s : $s;
                }
                switch ($type) {
                    case 'css':
                        $out_put .= '<link href="' . trim($s) . '" rel="stylesheet" media="screen" type="text/css" />' . "\n";
                        break;
                    case 'script':
                        $out_put .= '<script type="text/javascript" charset="' . $this->charset . '" src="' . trim($s) . '"></script>' . "\n";
                        break;
                }
            }
        }
        return $out_put ? '<?php echo ' . "'" . $out_put . "'" . ' ?>' : '';
    }
    /**
     * 拼装array 字符串  类似 var_export
     * @param array $ary
     */
    private function _parse_array($ary)
    {
        if (empty($ary) || !is_array($ary))
            return 'array()';
        $a = array(
            "/'/is",
            "/\\n/i",
            "/\\s(?=\\s)/i",
            "/[\n\r\t]/",
            '/\$(.*?)"\]/is',
            '/"\$/is',
            '/"\["/is',
            '/"\]"/is'
        );
        $b = array(
            '"',
            '',
            '',
            '',
            '"."$\\1"]."',
            '$',
            '["',
            '"]'
        );
        return str_replace(array(
            '"".',
            '"].["'
        ), array(
            '',
            '"]["'
        ), preg_replace($a, $b, stripslashes(var_export($ary, true))));
    }
    /**
     * 解析类似于  {building_link model='test' action='do' param="id=10&tets=do&show=$task&tioy=$show&j=task&yo=dot"} 参数里一大堆变量
     * @param $str
     */
    function _parse_arg_two($str)
    {
        $ary = array_filter(explode('&', $str));
        if (empty($ary))
            return '';
        $a = array(
            '/\$(.*?)_kk_/is',
            '/_kk_/is'
        );
        $b = array(
            '$this->_tpl_var[\'\\1\']',
            ''
        );
        foreach ($ary as &$tag) {
            $tag .= '_kk_';
            $tag = preg_replace($a, $b, $tag);
        }
        return str_replace(array(
            '$this->_tpl_var[\'this',
            '\']\']'
        ), array(
            '$this',
            '\']'
        ), join('&', $ary));
    }
    /**
     * 处理插件解析的数据
     */
    private function _parse_arg_for_plugin($tag)
    {
        if (empty($tag))
            return '';
        $data = array_filter($this->_str_trim($tag));
        if (empty($data) || !is_array($data))
            return array();
        array_shift($data);
        if (empty($data) || !is_array($data))
            return array();
        $ary_data = array();
        $a        = array(
            "/'/is",
            '/"/is',
            '/\./is',
            '/\$(.*?)__jj__/is',
            '/__jj__/is'
        );
        $b        = array(
            '',
            '',
            '\'][\'',
            '$this->_tpl_var[\'\\1\']',
            ''
        );
        $res      = array();
        foreach ($data as &$temp) {
            $temp .= '__jj__';
            $temp   = preg_replace($a, $b, $temp);
            $t_keys = explode('=', $temp);
            if (substr_count($temp, '=') >= 2) {
                $t = $t_keys;
                array_shift($t);
                $str = join('=', $t);
                if (false != strpos($str, '$')) {
                    $str = $this->_parse_arg_two($str);
                }
                $t_keys[1] = $str;
            }
            $res[$t_keys[0]] = $t_keys[1];
        }
        unset($t_keys, $temp, $data, $tag);
        return $res;
    }
    /**
     * 解析模板文件 内部函数
     * @param string $source
     */
    private function _fetch_str($source)
    {
        $res = preg_replace("/{([^\\}\\{\n]*)}/e", "\$this->_parse_tpl_variable('\\1');", $this->_smarty_pre_filter_compile($source));
        return preg_replace(array(
            /*"/__e\\(([\\s\\S]*?)\\)/is",
            "/__r\\(([\\s\\S]*?)\\)/is",*/
            "/<\\%([\\S\\s]*?)\\%>/is"
            
        ), array(
            /*"<?php __e(\\1); ?>",//语言包输出
            "<?php __r(\\1); ?>",//返回值*/
            "<?php \\1 ?>"
        ), $res);
        /*处理类似 <% function 的代码 %>*/
    }
    /**
     * 处理smarty标签中的变量标签
     * @access  public
     * @param   string     $val
     * @return  bool
     */
    private function _get_val($val)
    {
        /*处理类似 $item[0].key*/
        if (strrpos($val, '[') !== false)
            $val = preg_replace("/\\[([^\\[\\]]*)\\]/eis", "'.'.str_replace('$','\$','\\1')", $val);
        $result = '';
        if (strrpos($val, '|') !== false) {
            $moddb = explode('|', $val);
            $val   = array_shift($moddb);
        }
        if (empty($val))
            return '';
        if (strpos($val, '.$') !== false) {
            $all = explode('.$', $val);
            foreach ($all as $key => $val) {
                $all[$key] = $key == 0 ? $this->_make_var($val) : '[' . $this->_make_var($val) . ']';
            }
            $res = join('', $all);
        } else {
            $result = $this->_make_var($val);
        }
        if (!empty($moddb)) {
            foreach ($moddb as $key => $mod) {
                $s = explode(':', $mod);
                switch ($s[0]) {
                    case 'escape':
                        $s[1] = trim($s[1], '"');
                        if ($s[1] == 'html') {
                            $result = 'htmlspecialchars(' . $result . ')';
                        } elseif ($s[1] == 'url') {
                            $result = 'urlencode(' . $result . ')';
                        } elseif ($s[1] == 'decode_url') {
                            $result = 'urldecode(' . $result . ')';
                        } elseif ($s[1] == 'quotes') {
                            $result = 'addslashes(' . $result . ')';
                        } else {
                            $result = 'htmlspecialchars(' . $result . ')';
                        }
                        break;
                    case 'date_format':
                        array_shift($s);
                        $s      = join(':', $s);
                        $result = 'smarty_date_format(' . $result . ',' . $s . ')';
                        break;
                    case 'date':
                        array_shift($s);
                        $s      = join(':', $s);
                        $result = 'date(' . $result . ',time())';
                        break;
                    case '@print_r':
                    case 'print_r':
                        $result = '$this->_dump(' . $result . ')';
                        break;
                    case 'nl2br':
                        $result = 'nl2br(' . $result . ')';
                        break;
                    case 'default':
                        $s[1]   = $s[1]{0} == '$' ? $this->_get_val(substr($s[1], 1)) : "$s[1]";
                        $result = 'empty(' . $result . ') ? ' . $s[1] . ' : ' . $result;
                        break;
                    case 'truncate':
                        $l      = intval($s[1]);
                        $dot    = str_replace(array(
                            '"',
                            "'"
                        ), '', $s[2]);
                        $result = 'smarty_truncate(' . $result . ',' . $s[1] . ',0,"' . $dot . '")';
                        break;
                    case 'strip_tags':
                        $result = 'strip_tags(' . $result . ')';
                        break;
                    default:
                        $e      = $s[0];
                        $result = "$e($result)";
                        break;
                }
            }
        }
        return $result;
    }
    private function _dump($str)
    {
        echo '<pre style="text-align:left;">';
        print_r($str);
        echo '</pre>';
    }
    /**
     * 处理去掉$的字符串 此算法对付多种操作会有问题 比如 {$item.id * 10 + $item.id - $id } 只适用于简单的加减乘除 若想复杂运算 在模板里写PHPP计算吧
     * @access  public
     * @param   string     $val
     * @return  bool  
     */
    protected function _make_var($val)
    {
        $val = trim($val);
        if (strrpos($val, '.') === false) {
            if (isset($this->_tpl_var[$val]) && isset($this->_patchstack[$val]))
                $val = $this->_patchstack[$val];
            /*加法 除法 减法*/
            $p .= '$this->_tpl_var[\'' . str_replace(array(
                '+',
                '/',
                '-',
                '*',
                " "
            ), array(
                "'] + ",
                "'] / ",
                "'] - ",
                "'] * ",
                ""
            ), trim($val)) . (strpos($val, '+') !== false || strpos($val, '/') !== false || strpos($val, '-') !== false || strpos($val, '*') !== false ? '' : '\']');
        } else {
            $t         = explode('.', $val);
            $_var_name = array_shift($t);
            if (isset($this->_tpl_var[$_var_name]) && isset($this->_patchstack[$_var_name]))
                $_var_name = $this->_patchstack[$_var_name];
            $p = $_var_name == 'smarty' ? $this->_compile_smarty_ref($t) : '$this->_tpl_var[\'' . $_var_name . '\']';
            foreach ($t as $val) {
                if (false !== strpos($val, '*') || false !== strpos($val, '+') || false !== strpos($val, '-') || false !== strpos($val, '/')) {
                    /*加法 除法 减法 乘法*/
                    $p .= "['" . str_replace(array(
                        '*',
                        '+',
                        '-',
                        '/',
                        '$',
                        " "
                    ), array(
                        "']  *  ",
                        "']  +  ",
                        "']  -  ",
                        "']  /  ",
                        '$this->_tpl_var[\'',
                        ''
                    ), trim($val)) . (strpos($val, '$') !== false ? '\']' : '');
                } else {
                    $p .= '[\'' . $val . '\']';
                }
            }
        }
        return $p;
    }
    /**
     * 解析模板变量
     * @access  public
     * @param   string     $val
     * @param   int         $type
     * @return  array
     */
    protected function _parse_attrs($val, $type = 1)
    {
        $pa = $this->_str_trim($val);
        foreach ($pa as $value) {
            if (strrpos($value, '=')) {
                list($a, $b) = explode('=', str_replace(array(
                    ' ',
                    '"',
                    "'",
                    '&quot;'
                ), '', $value));
                if ($b{0} == '$') {
                    if ($type) {
                        eval('$para[\'' . $a . '\']=' . $this->_get_val(substr($b, 1)) . ';');
                    } else {
                        $para[$a] = $this->_get_val(substr($b, 1));
                    }
                } else {
                    $para[$a] = $b;
                }
            }
        }
        return $para;
    }
    /**
     * 处理if标签
     * @access  public
     * @param   string     $tag_args
     * @param   bool       $elseif
     * @return  string
     */
    protected function _compile_if_tag($tag_args, $elseif = false)
    {
        preg_match_all('/\-?\d+[\.\d]+|\'[^\'|\s]*\'|"[^"|\s]*"|[\$\w\.]+|!==|===|==|!=|<>|<<|>>|<=|>=|&&|\|\||\(|\)|,|\!|\^|=|&|<|>|~|\||\%|\+|\-|\/|\*|\@|\S/', $tag_args, $match);
        $tokens      = $match[0];
        $token_count = array_count_values($tokens);
        for ($i = 0, $count = count($tokens); $i < $count; $i++) {
            $token =& $tokens[$i];
            switch (strtolower($token)) {
                case 'ne':
                case 'neq':
                    $token = '!=';
                    break;
                case 'le':
                case 'lte':
                    $token = '<=';
                    break;
                case 'ge':
                case 'gte':
                    $token = '>=';
                    break;
                case 'gt':
                    $token = '>';
                    break;
                case 'eq':
                    $token = '==';
                    break;
                case 'lt':
                    $token = '<';
                    break;
                case 'and':
                    $token = '&&';
                    break;
                case 'or':
                    $token = '||';
                    break;
                case 'not':
                    $token = '!';
                    break;
                case 'mod':
                    $token = '%';
                    break;
                default:
                    if ($token[0] == '$')
                        $token = $this->_get_val(substr($token, 1));
            }
        }
        return $elseif ? '<?php }elseif (' . implode(' ', $tokens) . '){ ?>' : '<?php if (' . implode(' ', $tokens) . '){ ?>';
    }
    /**
     * 处理foreach标签
     * @access  public
     * @param   string     $tag_args
     * @return  string
     */
    protected function _compile_foreach_start($tag_args)
    {
        $attrs = $this->_parse_attrs($tag_args, 0);
        $keys  = array_keys($attrs);
        if (!in_array('item', $keys))
            return $this->_error('not set the item tag!');
        $arg_list = array();
        $from     = $attrs['from'];
        if (isset($this->_tpl_var[$attrs['item']]) && !isset($this->_patchstack[$attrs['item']])) {
            $this->_patchstack[$attrs['item']] = $attrs['item'] . '_' . str_replace(array(
                ' ',
                '.'
            ), '_', microtime());
            $attrs['item']                     = $this->_patchstack[$attrs['item']];
        } else {
            $this->_patchstack[$attrs['item']] = $attrs['item'];
        }
        $item = $this->_get_val($attrs['item']);
        if (!empty($attrs['key'])) {
            $key      = $attrs['key'];
            $key_part = $this->_get_val($key) . ' => ';
        } else {
            $key      = null;
            $key_part = '';
        }
        if (!empty($attrs['name'])) {
            $name = $attrs['name'];
        } else {
            $name = null;
        }
        $output = '<?php ';
        $output .= "\$_from = $from; if (!is_array(\$_from) && !is_object(\$_from)) { settype(\$_from, 'array'); }; \$this->_push_vars('$attrs[key]', '$attrs[item]');";
        if (!empty($name)) {
            $foreach_props = "\$this->_foreach['$name']";
            $output .= "{$foreach_props} = array('total' => count(\$_from), 'iteration' => 0);\n";
            $output .= "if ({$foreach_props}['total'] > 0){\n";
            $output .= " foreach (\$_from AS $key_part$item){\n";
            $output .= "  {$foreach_props}['iteration']++;\n";
        } else {
            $output .= "if (count(\$_from)){\n";
            $output .= "    foreach (\$_from AS $key_part$item){\n";
        }
        return $output . '?>';
    }
    /**
     * 将 foreach 的 key, item 放入临时数组
     * @param  mixed    $key
     * @param  mixed    $val
     * @return  void
     */
    protected function _push_vars($key, $val)
    {
        if (!empty($key))
            array_push($this->_temp_key, "\$this->_tpl_vars['$key']='" . $this->_tpl_vars[$key] . "';");
        if (!empty($val))
            array_push($this->_temp_val, "\$this->_tpl_vars['$val']='" . $this->_tpl_vars[$val] . "';");
    }
    /**
     * 弹出临时数组的最后一个
     * @return  void
     */
    protected function _pop_vars()
    {
        $key = array_pop($this->_temp_key);
        $val = array_pop($this->_temp_val);
        if (!empty($key))
            eval($key);
    }
    /**
     * 处理smarty开头的预定义变量
     * @access  public
     * @param   array   $indexes
     * @return  string
     */
    protected function _compile_smarty_ref(&$indexes)
    {
        $_ref = $indexes[0];
        switch ($_ref) {
            case 'now':
                $compiled_ref = 'time()';
                break;
            case 'foreach':
                array_shift($indexes);
                $_var      = $indexes[0];
                $_propname = $indexes[1];
                switch ($_propname) {
                    case 'index':
                        array_shift($indexes);
                        $compiled_ref = "(\$this->_foreach['$_var']['iteration'] - 1)";
                        break;
                    case 'first':
                        array_shift($indexes);
                        $compiled_ref = "(\$this->_foreach['$_var']['iteration'] <= 1)";
                        break;
                    case 'last':
                        array_shift($indexes);
                        $compiled_ref = "(\$this->_foreach['$_var']['iteration'] == \$this->_foreach['$_var']['total'])";
                        break;
                    case 'show':
                        array_shift($indexes);
                        $compiled_ref = "(\$this->_foreach['$_var']['total'] > 0)";
                        break;
                    default:
                        $compiled_ref = "\$this->_foreach['$_var']";
                        break;
                }
                break;
            case 'globals':
                $compiled_ref = '$GLOBALS';
                break;
            case 'get':
                $compiled_ref = '$_GET';
                break;
            case 'post':
                $compiled_ref = '$_POST';
                break;
            case 'cookies':
                $compiled_ref = '$_COOKIE';
                break;
            case 'env':
                $compiled_ref = '$_ENV';
                break;
            case 'server':
                $compiled_ref = '$_SERVER';
                break;
            case 'request':
                $compiled_ref = '$_REQUEST';
                break;
            case 'session':
                $compiled_ref = '$_SESSION';
                break;
            default:
                break;
        }
        array_shift($indexes);
        return $compiled_ref;
    }
    protected function _str_trim($str)
    {
        while (strpos($str, '= ') != 0) {
            $str = str_replace('= ', '=', $str);
        }
        while (strpos($str, ' =') != 0) {
            $str = str_replace(' =', '=', $str);
        }
        return explode(' ', trim($str));
    }
    /**
     * 执行PHP  默认不返回数据 直接输出来
     * @param $content
     * @param $return
     */
    protected function _eval($content, $return = true)
    {
        $content = trim($content);
        if (empty($content))
            return $content;
        $result = '';
        if ($return)
            @ob_start();
        eval('?>' . $content);
        /*刷新输出缓冲数据*/
        if (!$return)
            $this->flush();
        if ($return) {
            $result = ob_get_contents();
            @ob_end_clean();
        }
        return $result;
    }
    function get_var($str = '')
    {
        if (empty($str))
            return $this->_tpl_var;
        $s  = '';
        $ee = explode('.', $str);
        foreach ($ee as $v) {
            $s .= "['" . $v . "']";
        }
        $s .= ";";
        $estring = '';
        $j       = '$estring = $this->_tpl_var' . $s;
        eval($j);
        return $estring;
    }
}
/*外部函数*/
function smarty_make_timestamp($string)
{
    if (empty($string)) {
        $time = time();
    } elseif (preg_match('/^\d{14}$/', $string)) {
        $time = mktime(substr($string, 8, 2), substr($string, 10, 2), substr($string, 12, 2), substr($string, 4, 2), substr($string, 6, 2), substr($string, 0, 4));
    } elseif (is_numeric($string)) {
        $time = (int) $string;
    } else {
        $time = strtotime($string);
        if ($time == -1 || $time === false) {
            $time = time();
        }
    }
    return $time;
}
function smarty_date_format($string, $format = '%b %e, %Y', $default_date = '')
{
    if ($string != '') {
        $timestamp = smarty_make_timestamp($string);
    } elseif ($default_date != '') {
        $timestamp = smarty_make_timestamp($default_date);
    } else {
        return;
    }
    if (DIRECTORY_SEPARATOR == '\\') { //win主机
        $_win_from = array(
            '%D',
            '%h',
            '%n',
            '%r',
            '%R',
            '%t',
            '%T'
        );
        $_win_to   = array(
            '%m/%d/%y',
            '%b',
            "\n",
            '%I:%M:%S %p',
            '%H:%M',
            "\t",
            '%H:%M:%S'
        );
        if (strpos($format, '%e') !== false) {
            $_win_from[] = '%e';
            $_win_to[]   = sprintf('%\' 2d', date('j', $timestamp));
        }
        if (strpos($format, '%l') !== false) {
            $_win_from[] = '%l';
            $_win_to[]   = sprintf('%\' 2d', date('h', $timestamp));
        }
        $format = str_replace($_win_from, $_win_to, $format);
    }
    return strftime($format, $timestamp);
}
function smarty_truncate($needCutString = '', $end_length = 20, $start = 0, $endstring = '...')
{
    $i      = 0;
    $output = "";
    while ($i < strlen($needCutString)) {
        if ($start-- == 0) {
            break;
        }
        if (ord(substr($needCutString, $i, 1)) > 128) {
            $i++;
        }
        $i++;
    }
    for (; $i < strlen($needCutString); $i++) {
        if (ord(substr($needCutString, $i, 1)) > 128) {
            $output .= substr($needCutString, $i, 2);
            $i++;
        } else {
            $output .= substr($needCutString, $i, 1);
        }
        if (--$end_length == 0)
            break;
    }
    return $output . $endstring;
}
/*####################################smarty 插件处理函数########################################*/
/**
 * 过滤处理函数 过滤所有在模板里的PHP代码 
 * @param $string
 */
function smarty_outputfilter_filter_phptag($string)
{
    $p = array(
        '/\{\*\}([\S\s]*?)\{\*\}/is',
        /*删smarty标记 {*}[code]{*}*/
        '/<!--([\S\s]*?)-->/i',
        /*删html注释*/
        '/<\\?(.+?)\\?>/is',
        '/<\\?(.+?)/is',
        /*删<?代码 非闭合*/
        '/\/\*([\S\s]*?)\*\//is'
        /*删 星号*/
        /* '/<\\?(.+?)\\?>/is',/*删php 闭合代码*/
    );
    $r = array(
        '',
        '',
        '',
        ''
    );
    return trim(preg_replace($p, $r, $string));
}
?>