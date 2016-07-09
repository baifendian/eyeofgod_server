<?php
	if(@strpos($_SERVER[HTTP_USER_AGENT], "MSIE 6.0")){
		header("Location: ./no_ie6.html\n");
		exit('');
	}
	set_time_limit(0);
	//项目调试模式
	//define('RUNTIME_MODEL', true);
	define('SEND_DEBUG_INFO', false);
  	define('SITE_ROOT', str_replace('\\', '/', realpath('./')).'/');
  	define('SITE_RUNTIM_PATH', SITE_ROOT.'Runtime/');
  	define('SITE_TEMP_PATH', SITE_RUNTIM_PATH.'Temp/');
  	define('ROOT_PATH',SITE_ROOT);
	//定ThinkPHP框架路徂
	define('THINK_PATH', './ThinkPHP/');
	//定顷目名称和路徂
	define('APP_NAME', 'eyeofgod');
	define('APP_PATH', SITE_ROOT);
	define('__APP__', '');
	//加载框架入口文件
  	@ini_set('memory_limit',          '256M');
	require(THINK_PATH."ThinkPHP.php");
	@ini_set('session.use_trans_sid', 0);
	@ini_set('session.use_cookies',   1);
	@ini_set('session.use_only_cookies',1);
	@ini_set('session.cookie_httponly',true);
	@session_name('__APP_PUB__');

	/***********************************************致命异常捕获*********************************************************************/
	function __mkdirs_a($path, $mode = 0777) {
		$path = str_replace("\\", '/', $path);
		$dirs = explode('/', ($path = substr($path, -1, 1) !== '/' ? $path . '/' : $path));
		$pos = strrpos($path, ".");
		$subamount = $pos === false ? 0 : 1;
		for ($c = 0; $c < count($dirs) - $subamount; $c++) {
			$thispath = "";
			for ($cc = 0; $cc <= $c; $cc++) {
				$thispath.=$dirs[$cc] . '/';
			}
			if (!file_exists($thispath)
			)@mkdir($thispath, $mode);
		}
	}

	function __create_file($data, $file, $cover="wb+", $mod = 0777) {//ab追加 WB 覆盖
		@__mkdirs_a(dirname($file));
		$fp = fopen($file, $cover);
		fwrite($fp, $data); //="\xEF\xBB\xBF".$text;
		@flock($fp, LOCK_EX);
		@fclose($fp);
		@chmod($file, $mod);
		return!is_file($file) ? false : true;
	}
	
	function __send_mail__($reciver=array(), $subject = '', $body = '', $add_other_replay='', $AddAttachment=array()) {
	    if (empty($reciver))return false;
	    $config =  require_once SITE_ROOT.'Conf/base.config.php';
	    if($config['IS_SEND_MAIL_OPEN']){
		    $file = SITE_ROOT.'log/email/'.date("Ymd").'/date_'.date("YmdHis").'.php';
		    $fdata = '<?php exit("0"); ?>'."\r\n".var_export(func_get_args(),true);
		    __create_file($fdata, $file);
		    return false;
	    }
	    require_once dirname(__FILE__).'/Lib/ORG/phpmail/phpmail.class.php';
	    $config = include dirname(__FILE__).'/Conf/mail.config.php';
	    $mail = new PHPMailer();
	    $mail->CharSet = 'UTF-8'; /* 设置邮件编码 */
	    $from = $config["ADMIN_EMAIL"];
	    $mail->From = $from;
	    $from_name = $config["ADMIN_EMAIL_NAME"];
	    $mail->FromName = $from_name;
	    $reciver = (array) $reciver;
	    /* 处理多个收信人 */
	    foreach ($reciver as $r) {
	        $mail->AddAddress($r, array_shift(explode('@', $r)));
	    }
	    $mail->WordWrap = 50;
	    $mail->Subject = strip_tags($subject);
	    $mail->AltBody = ($body); /* 如果邮箱不支持HTML就用他 */
	    $mail->IsHTML(true);
	    /* 处理回复人 */
	    $replay_mail = !empty($add_other_replay) ? $add_other_replay : $from;
	    $mail->AddReplyTo($replay_mail, $from_name);
	    $mail_type = $config["SENDMAIL_TYPE"];
	    switch ($mail_type) {/* 获得站内邮箱配置 */
	        case 'mail':
	            $mail->MsgHTML($body);
	            break;
	        case 'sendmail':
	            $mail->IsSendmail();
	            $mail->MsgHTML($body);
	            break;
	        case 'smtp':
	            $mail->IsSMTP();
	            $mail->Host = trim($config["SMTP_SERVER"]);
	            $mail->SMTPAuth = true;
	            $mail->Port = $config['SMTP_PORT'];
	            $mail->Username = trim($config["SMTP_USERMAIL"]);
	            $mail->Password = trim($config["SMTP_PASSWORD"]);
	            $mail->MsgHTML($body);
	            break;
	        default:return FALSE;
	    }
	    $AddAttachment= (array)$AddAttachment;
	    if (!empty($AddAttachment)) {
	        foreach ($AddAttachment as $k => $item) {
	            $name = pathinfo($item, PATHINFO_BASENAME);
	            $name = !is_numeric($k) ? $name : $name;
	            $mail->AddAttachment($item, $name);
	        }
	    }
	    return $mail->Send() ? TRUE : FALSE;
	}

	register_shutdown_function('__shutdown__');
	
	function FriendlyErrorType($type){
        switch($type){
         	case '1':
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
            case '2':
                return 'E_WARNING';
            case E_PARSE: // 4 //
           	case '4':
                return 'E_PARSE';
            case E_NOTICE: // 8 //
           	case '8':
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
           	case '16':
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
            case '32':
                return 'E_CORE_WARNING';
            case E_CORE_ERROR: // 64 //
           	case '64':
                return 'E_COMPILE_ERROR';
            case E_CORE_WARNING: // 128 //
           	case '128':
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
           	case '256':
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
           	case '512':
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
           	case '1024':
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
           	case '2048':
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
           	case '4096':
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
           	case '8192':
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
           	case '16384':
                return 'E_USER_DEPRECATED';
		}
        return $type;
    } 
	
    function __shutdown__($log_all = false){
		$error = error_get_last();
		$error_type = $error['type'];
		$error['_type_arg_'] = FriendlyErrorType($error_type);
		$send_error = false;
		if(in_array($error_type, array(1,16,32,64,128,256,2048)) && !$log_all){
			$send_error = true;
		}
		if($log_all){
	   		$send_error = true;
		}
		if($send_error){
			__sent_mail($error);
		}
	}
	
	function __sent_mail($error){
		if(SEND_DEBUG_INFO==true){
			$msg = "LAST ERROR <hr />\n\n". var_export($error,true)."\r\n<hr />";
		     $msg .= ob_get_contents()."\r\n<hr />";
		     $msg .= var_export(array('server'=>$_SERVER),true)."\r\n<hr />";
		     $msg .= var_export(array('request'=>$_REQUEST),true)."\r\n<hr />";
		     $msg .='<hr />';
		     $msg .= var_export(get_included_files(),true);
		     $config = include dirname(__FILE__).'/Conf/mail.config.php';
		     $reciver_default = array('feng.yin@baifendian.com');
		     $reciver = $config['SYSTEM_ERROR_SEND_MAIL'];
		     $reciver = empty($reciver)?$reciver_default:$reciver;
		     $subject= '[ATD]广告平台项目程序发生异常,请检查!'.date("Y-m-d H:i:s");	
		     __send_mail__($reciver, $subject, $msg);
		}
	}
	
	function __shutdown__and_send(){
     	$error = error_get_last();
     	$error_type = $error['type'];
     	__sent_mail($error);
	}
	/***********************************************致命异常捕获结束*********************************************************************/
	
 	//实例化一个网站应用实例
 	App::run();
?>