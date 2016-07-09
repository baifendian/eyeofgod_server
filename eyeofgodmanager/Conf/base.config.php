<?php 
//系统基础配置
return array(
	'SITE_PATH'=>SITE_ROOT,
    'SITE_TEMP_PATH'=>SITE_ROOT.'Runtime/Temp/', //获取系统缓存目录
    'DEFAULT_MODULE' => 'Home',
	'CALL_DDP_DEBUG'=>true,//请求DDP调试模式 默认开启 若为false 不开启
    'URL_CASE_INSENSITIVE' => true,
    'DEBUG_MODE' => false,
	'OPEN_VERIFY' => true,//验证码
    'APP_DEBUG' => false, //开启调试模式
	'LOG_RECORD' => true, // 开启日志记录
    'LOG_RECORD_LEVEL' => array('EMERG', 'ALERT', 'CRIT', 'ERR'),
    'DATA_CACHE_TYPE' => 'File', //设置缓存类型
    //路由设置开启
    'URL_ROUTER_ON'=>true,
    'URL_MODEL'=>'2',
    'URL_PATHINFO_MODEL'=>'2',
	'URL_HTML_SUFFIX'=>'.html',
	/**
	'URL_MODEL'      => 1,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式) 当URL_DISPATCH_ON开启后有效; 默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_PATHINFO_MODEL'    => 2,       // PATHINFO 模式,使用数字1、2、3代表以下三种模式:
    // 1 普通模式(参数没有顺序,例如/m/module/a/action/id/1);
    // 2 智能模式(系统默认使用的模式，可自动识别模块和操作/module/action/id/1/ 或者 /module,action,id,1/...);
    // 3 兼容模式(通过一个GET变量将PATHINFO传递给dispather，默认为s index.php?s=/module/action/id/1)
    'URL_PATHINFO_DEPR'     => '/',	// PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置
    //'URL_AUTO_REDIRECT'     => true, // 自动重定向到规范的URL 不再生效
	*/
    
    'IS_OPEN_ERROR_HANDLE'=>false,
	'IS_OPEN_SIGNLE_USER_LOGIN'=>false,//是否开启独占用户登录 当开启后同一用户在线只能一个 ，不能多客户端或多浏览器登录 会强制踢出原已登录用户
	'OPEN_DB_ERROR_MSG'=>true, /*DB报错开关*/
	'IS_SEND_MAIL_OPEN'=>false,//是否开启发送邮件 若不发邮件 写日志 默认为false  当为true时关闭发送邮件功能 直接写日志  总开关 一旦为true 则全部发不了邮件
	//权限配置
    'USER_AUTH_ON' => true,
    'USER_AUTH_KEY' => 'authId', // 用户认证SESSION标记
    'FILE_LINK_UPLOAD_PICTURE'=>'upload/', //本地合作伙伴和焦点图上传文件夹
    '__CLIENT_NO__'=>'1',//平台编号
	/***发送给管理员的邮件配置***/
	'TO_ADMIN_MAIL_LIST'=>array(
		'feng.yin@baifendian.com',
	),
	//系统发生错误时邮件发送
	'SYSTEM_ERROR_SEND_MAIL'=>array(
		'feng.yin@baifendian.com',
	),
);
