<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * ThinkPHP Action控制器基类 抽象类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
abstract class Action extends Think
{//类定义开始

    // 视图实例对象
    protected $view   =  null;
    // 当前Action名称
    private $name =  '';

   /**
     +----------------------------------------------------------
     * 架构函数 取得模板对象实例
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function __construct()
    {
        //实例化视图类
        //$this->view       = Think::instance('View');
        //控制器初始化
        if(method_exists($this,'_initialize')) $this->_initialize();
    }
	function _initialize(){}
   /**
     +----------------------------------------------------------
     * 获取当前Action名称
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     */
    protected function getActionName() {
        if(empty($this->name)) {
            // 获取Action名称
            $this->name     =   substr(get_class($this),0,-6);
        }
        return $this->name;
    }

    /**
     +----------------------------------------------------------
     * 是否AJAX请求
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
    protected function isAjax() {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
            if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return true;
        }
        if(!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')]))
            // 判断Ajax方式提交
            return true;
        return false;
    }

    /**
     +----------------------------------------------------------
     * 模板显示
     * 调用内置的模板引擎显示方法，
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function display($templateFile='',$charset='',$contentType='text/html')
    {
        /*if(false === $templateFile) {
            $this->showTrace();
        }else{
            $this->view->display($templateFile,$charset,$contentType);
        }*/
    }

    /**
     +----------------------------------------------------------
     *  获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function fetch($templateFile='',$charset='',$contentType='text/html')
    {
       /* return $this->view->fetch($templateFile,$charset,$contentType);*/
    }

    /**
     +----------------------------------------------------------
     *  创建静态页面
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @htmlfile 生成的静态文件名称
     * @htmlpath 生成的静态文件路径
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function buildHtml($htmlfile='',$htmlpath='',$templateFile='',$charset='',$contentType='text/html') {
        /*return $this->view->buildHtml($htmlfile,$htmlpath,$templateFile,$charset,$contentType);*/
    }

    /**
     +----------------------------------------------------------
     * 模板变量赋值
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function assign($name,$value='')
    {
       /* $this->view->assign($name,$value);*/
    }

    public function __set($name,$value) {
        /*$this->view->assign($name,$value);*/
    }

    /**
     +----------------------------------------------------------
     * 取得模板显示变量的值
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $name 模板显示变量
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    protected function get($name)
    {
        /*return $this->view->get($name);*/
    }

    public function __get($name) {
       // return $this->view->get($name);
    }

    /**
     +----------------------------------------------------------
     * Trace变量赋值
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function trace($name,$value='')
    {
        //$this->view->trace($name,$value);
    }

    /**
     +----------------------------------------------------------
     * 魔术方法 有不存在的操作的时候执行
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $method 方法名
     * @param array $parms 参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __call($method,$parms) {

        if( 0 === strcasecmp($method,ACTION_NAME)) {
            // 检查扩展操作方法
            $_action = C('_actions_');
            if($_action) {
                // 'module:action'=>'callback'
                if(isset($_action[MODULE_NAME.':'.ACTION_NAME])) {
                    $action  =  $_action[MODULE_NAME.':'.ACTION_NAME];
                }elseif(isset($_action[ACTION_NAME])){
                    // 'action'=>'callback'
                    $action  =  $_action[ACTION_NAME];
                }
                if(!empty($action)) {
                    call_user_func($action);
                    return ;
                }
            }
            // 如果定义了_empty操作 则调用
            if(method_exists($this,'_empty')) {
                $this->_empty($method,$parms);
            }else {
                // 检查是否存在默认模版 如果有直接输出模版
                if(file_exists_case(C('TMPL_FILE_NAME')))
                    $this->display();
                else
                    // 抛出异常
                    throw_exception(L('_ERROR_ACTION_').ACTION_NAME);
            }
        }elseif(in_array(strtolower($method),array('ispost','isget','ishead','isdelete','isput'))){
            return strtolower($_SERVER['REQUEST_METHOD']) == strtolower(substr($method,2));
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
        }
    } 

    protected function showTrace(){
        //$this->view->traceVar();
    }

}//类定义结束
?>