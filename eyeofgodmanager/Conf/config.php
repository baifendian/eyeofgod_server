<?php
//核心配置加载器
if(!function_exists('_eog_load_config')){
	/* 加载文件*/
	function _eog_load_config(){
		$dir = dirname(__FILE__).'/';
		$dh = opendir($dir);
		$result = array();
		while (false!=$file = readdir($dh)){
			$ext = substr($file, -11,12);
			if($file!='.' && $file!='..' && $ext == '.config.php'){
				$result = array_merge($result,include ($dir.$file));
			}
			if (is_dir($dir.$file)) {
				$son_dir = $dir.$file.'/';
				$son_result = array();
				$son_dh = opendir($son_dir);
				while (false!=$son_file = readdir($son_dh)){
					$son_ext = substr($son_file, -11,12);
					if($son_file!='.' && $son_file!='..' && $son_ext == '.config.php'){
						$son_result = array_merge($son_result,include ($son_dir.$son_file));
					}
				}
				$result = array_merge($result,array($file=>$son_result));
				closedir($son_dh);
			}
		}
		closedir($dh);
		return $result;
	}
}
return _eog_load_config();
?>
