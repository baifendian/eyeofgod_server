<?php 
/**
 * 模板配置
 */
	return array(
		'TPL_OUTPUT_CHAR'=>'utf-8',
		'__add_form_hash__'=>false,/*是否增加表单过滤*/
		'TPL_DEVELOP_TYPE'=>true,/*是否开启调试模式*/
		'TPL_EXTENSION_PREFIX'=>'.php',
		'DEFAULT_THEME' => 'eyeOfGodV1.0',
		'TPL_DEBUG'=>false,
		'TPL_MAIN_PATH'=>SITE_ROOT.'Tpl/',
		'TPL_TEMP_PATH'=>SITE_ROOT.'Runtime/cache/',
		'TMPL_PARSE_STRING' => array(
			'__PUBLIC__' => '/Public',
			'__APP__' => '',
		),
	);
?>