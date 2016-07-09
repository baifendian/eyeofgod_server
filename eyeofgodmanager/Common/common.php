<?php
function check_string_ok($nickname) {
	$nickname = trim($nickname);
	$a=preg_match("/^[0-9A-Z_a-z]*$/", $nickname);
	if($a) return true;
	return false;
	
}

//解决php加载图片慢的问题，用样式控制图片不让图片失真
function getImgCss($stuff_length,$stuff_width,$page){
	$bilie = $stuff_length/$stuff_width;
	$config = array(
		'1'=>array('1'=>'thumb_nail','2'=>'thumb_nail2','3'=>'thumb_nail3'),
		'2'=>array('1'=>'thumb_nail4','2'=>'thumb_nail5','3'=>'thumb_nail6')
	);
	$index = 1;
	if ( $bilie<0.8 ) {
		$index = 2;
	} elseif ( $bilie>1.3 ) {
		$index = 3;
	}
	switch ($page){
		case 'creativelist':
			$style = 1;
			break;
		case 'addplan':
		case 'viewplan':
		case 'vieworder':
		case 'verifyplan':
		case 'vieworder':
		case 'memberInfo':
		case 'verifymemberInfo':
		case 'editmemberInfo':
			$style = 2;
			break;
		default:
			$style = 'error';
			break;
	}
	return $config[$style][$index];
}
///////////////解决bmp图片不能预览-开始
function imagecreatefrombmp($file)
{

	global  $CurrentBit, $echoMode;
	$f=fopen($file,"r");
	$Header=fread($f,2);
	
	if($Header=="BM")
	{
		$Size=freaddword($f);
		$Reserved1=freadword($f);
		$Reserved2=freadword($f);
		$FirstByteOfImage=freaddword($f);
	
		$SizeBITMAPINFOHEADER=freaddword($f);
		$Width=freaddword($f);
		$Height=freaddword($f);
		$biPlanes=freadword($f);
		$biBitCount=freadword($f);
		$RLECompression=freaddword($f);
		$WidthxHeight=freaddword($f);
		$biXPelsPerMeter=freaddword($f);
		$biYPelsPerMeter=freaddword($f);
		$NumberOfPalettesUsed=freaddword($f);
		$NumberOfImportantColors=freaddword($f);
	
		if($biBitCount<24)
		{
			$img=imagecreate($Width,$Height);
			$Colors=pow(2,$biBitCount);
			for($p=0;$p<$Colors;$p++)
			{
			$B=freadbyte($f);
			$G=freadbyte($f);
			$R=freadbyte($f);
			$Reserved=freadbyte($f);
			$Palette[]=imagecolorallocate($img,$R,$G,$B);
			}
	
	
	
	
			if($RLECompression==0)
			{
			$Zbytek=(4-ceil(($Width/(8/$biBitCount)))%4)%4;
	
			for($y=$Height-1;$y>=0;$y--)
			{
			$CurrentBit=0;
				for($x=0;$x<$Width;$x++)
				{
				$C=freadbits($f,$biBitCount);
				imagesetpixel($img,$x,$y,$Palette[$C]);
				}
				if($CurrentBit!=0) {freadbyte($f);}
				for($g=0;$g<$Zbytek;$g++)
					freadbyte($f);
			}
	
			}
			}
	
	
			if($RLECompression==1) //$BI_RLE8
			{
			$y=$Height;
	
			$pocetb=0;
	
			while(true)
			{
			$y--;
			$prefix=freadbyte($f);
				$suffix=freadbyte($f);
				$pocetb+=2;
	
				$echoit=false;
	
				if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
				if(($prefix==0)and($suffix==1)) break;
				if(feof($f)) break;
	
				while(!(($prefix==0)and($suffix==0)))
				{
				if($prefix==0)
				{
				$pocet=$suffix;
				$Data.=fread($f,$pocet);
				$pocetb+=$pocet;
				if($pocetb%2==1) {freadbyte($f); $pocetb++;}
				}
				if($prefix>0)
				{
				$pocet=$prefix;
				for($r=0;$r<$pocet;$r++)
					$Data.=chr($suffix);
				}
				$prefix=freadbyte($f);
				$suffix=freadbyte($f);
				$pocetb+=2;
				if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
				}
	
				for($x=0;$x<strlen($Data);$x++)
				{
				imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
			}
			$Data="";
	
			}
	
			}
	
	
			if($RLECompression==2) //$BI_RLE4
			{
			$y=$Height;
			$pocetb=0;
	
			/*while(!feof($f))
				echo freadbyte($f)."_".freadbyte($f)."<BR>";*/
				while(true)
			{
			 //break;
			$y--;
			$prefix=freadbyte($f);
			$suffix=freadbyte($f);
			$pocetb+=2;
	
			$echoit=false;
	
				if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
				if(($prefix==0)and($suffix==1)) break;
				if(feof($f)) break;
	
				while(!(($prefix==0)and($suffix==0)))
				{
				if($prefix==0)
				{
				$pocet=$suffix;
	
				$CurrentBit=0;
				for($h=0;$h<$pocet;$h++)
					$Data.=chr(freadbits($f,4));
					if($CurrentBit!=0) freadbits($f,4);
					$pocetb+=ceil(($pocet/2));
					if($pocetb%2==1) {freadbyte($f); $pocetb++;}
					}
					if($prefix>0)
					{
					$pocet=$prefix;
					$i=0;
					for($r=0;$r<$pocet;$r++)
					{
					if($i%2==0)
					{
					$Data.=chr($suffix%16);
					}
					else
					{
					$Data.=chr(floor($suffix/16));
					}
					$i++;
					}
					}
					$prefix=freadbyte($f);
					$suffix=freadbyte($f);
						$pocetb+=2;
						if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
					}
	
					for($x=0;$x<strlen($Data);$x++)
					{
					imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
			}
			$Data="";
	
			}
	
			}
	
	
			if($biBitCount==24)
			{
			$img=imagecreatetruecolor($Width,$Height);
			$Zbytek=$Width%4;
	
			for($y=$Height-1;$y>=0;$y--)
			{
			for($x=0;$x<$Width;$x++)
			{
			$B=freadbyte($f);
			$G=freadbyte($f);
			$R=freadbyte($f);
			$color=imagecolorexact($img,$R,$G,$B);
			if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
			imagesetpixel($img,$x,$y,$color);
			}
			for($z=0;$z<$Zbytek;$z++)
				freadbyte($f);
			}
			}
			return $img;
	
			}
	
	
	fclose($f);
}

function freadbyte($f)
{
	return ord(fread($f,1));
}

function freadword($f)
{
	$b1=freadbyte($f);
 	$b2=freadbyte($f);
	return $b2*256+$b1;
}

function freaddword($f)
{
	$b1=freadword($f);
	$b2=freadword($f);
	return $b2*65536+$b1;
}
///////////////解决bmp图片不能预览-结束

function subString($str, $start, $length) {
	$i = 0;
	//完整排除之前的UTF8字符
	while($i < $start) {
		$ord = ord($str{$i});
		if($ord < 192) {
			$i++;
		} elseif($ord <224) {
			$i += 2;
		} else {
			$i += 3;
		}
	}
	//开始截取
	$result = '';
	while($i < $start + $length && $i < strlen($str)) {
		$ord = ord($str{$i});
		if($ord < 192) {
			$result .= $str{$i};
			$i++;
		} elseif($ord <224) {
			$result .= $str{$i}.$str{$i+1};
			$i += 2;
		} else {
			$result .= $str{$i}.$str{$i+1}.$str{$i+2};
			$i += 3;
		}
	}
	if($i < strlen($str)) {
		$result .= '';
	}
	return $result;
}

function getFileType($filename)
{
	//1:gif,2:jpg,3:png,6:bmp
	if(function_exists('exif_imagetype')){
		return exif_imagetype($filename);
	}
	$file    = fopen($filename, "rb");    
	$bin    = fread($file, 2); //只读2字节   
	fclose($file);    
	$strInfo    = @unpack("C2chars", $bin);    
	$typeCode    = intval($strInfo['chars1'].$strInfo['chars2']);    
	$fileType    = '';    
	switch($typeCode){        
// 		case 7790:            
// 			$fileType = 'exe';break;        
// 		case 7784:            
// 			$fileType = 'midi'; break;        
// 		case 8297:            
// 			$fileType = 'rar'; break;        
		case 255216:            
			$fileType = '2';break;        
		case 7173:            
			$fileType = '1';break;        
		case 6677:            
			$fileType = '6';break;        
		case 13780:            
			$fileType = '3';break;        
		default:            
			$fileType = 'unknown';    
	}    
	return $fileType;
}
/**
 * 全局通用函数库
 * Enter description here ...
 * @param unknown_type $str
 */
function is_email($str)
{
    return @preg_match("/^\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*$/", $str);
}

/**
 * 检验url是否合法
 * @param    string      $str       待检验的url字符串
 * @return   boolean     true是合法的email,false为非法的url
 */
function is_url($str)
{
    return @preg_match("/^http:\\/\\/[A-Za-z0-9]+\\.[A-Za-z0-9]+[\\/=\\?%\\-&_~`@[\\]\\':+!]*([^<>\"])*$/", $str);
}

/**
 * 检验qq是否合法
 * @param    string      $str       待检验的qq字符串
 * @return   boolean     true是合法的qq,false为非法的qq
 */
function is_qq($str)
{
    return @preg_match("/^[1-9]\\d{4,9}$/", $str);
}

/**
 * 获得唯一hash值
 */
function get_uique_id()
{
    return md5(uniqid(time()));
}

/* 检验手机是否合法
 * @param    string      $str       待检验的手机号字符串
 * @return   boolean     true是合法的手机号,false为非法的手机号
 */

function is_phone($str)
{
    return @preg_match("/^(13|15|18|14|17)[0-9]{9}$/", $str);
}

function __init_request__(){
	$_REQUEST = html_encode($_REQUEST);
	$_POST = html_encode($_POST);
	$_GET = html_encode($_GET);
}
/**
 * 通用函数
 */

/**
 * 发送邮件
 * @param array $reciver 接收人
 * @param string $subject
 * @param string $body
 * @return boolean 若成功返回true
 */
function send_mail($reciver = array(), $subject = '', $body = '', $add_other_replay = '', $AddAttachment = array())
{
    $config = require_once SITE_ROOT . 'Conf/base.config.php';
    if ($config['IS_SEND_MAIL_OPEN']) {
        $file  = SITE_ROOT . 'log/email/' . date("Ymd") . '/date_' . date("YmdHis") . '.php';
        $fdata = '<?php exit("0"); ?>' . "\r\n" . var_export(func_get_args(), true);
        __create_file($fdata, $file);
        return false;
    }
    if (empty($reciver))
        return false;
    $reciver = !is_array($reciver) ? explode(',', $reciver) : (array) $reciver;
    import('@.ORG.phpmail.phpmail');
    $mail           = new PHPMailer();
    $mail->CharSet  = C("SENDMAIL_CHAR");
    /* 设置邮件编码 */
    $from           = C("ADMIN_EMAIL");
    $mail->From     = $from;
    $from_name      = C("ADMIN_EMAIL_NAME");
    $mail->FromName = $from_name;
    $reciver        = (array) $reciver;
    /* 处理多个收信人 */
    foreach ($reciver as $r) {
        $mail->AddAddress($r, array_shift(explode('@', $r)));
    }
    $mail->WordWrap = 50;
    $mail->Subject  = strip_tags($subject);
    $mail->AltBody  = ($body);
    /* 如果邮箱不支持HTML就用他 */
    $mail->IsHTML(true);
    /* 处理回复人 */
    $replay_mail = !empty($add_other_replay) ? $add_other_replay : $from;
    $mail->AddReplyTo($replay_mail, $from_name);
    $mail_type = C("SENDMAIL_TYPE");
    switch ($mail_type) {
        /* 获得站内邮箱配置 */
        case 'mail':
            $mail->MsgHTML($body);
            break;
        case 'sendmail':
            $mail->IsSendmail();
            $mail->MsgHTML($body);
            break;
        case 'smtp':
            $mail->IsSMTP();
            $mail->Host     = trim(C("SMTP_SERVER"));
            $mail->SMTPAuth = true;
            $mail->Port     = C('SMTP_PORT');
            $mail->Username = trim(C("SMTP_USERMAIL"));
            $mail->Password = trim(C("SMTP_PASSWORD"));
            $mail->MsgHTML($body);
            break;
        default:
            return FALSE;
    }
    $AddAttachment = (array) $AddAttachment;
    if (!empty($AddAttachment)) {
        foreach ($AddAttachment as $k => $item) {
            $name = pathinfo($item, PATHINFO_BASENAME);
            $name = !is_numeric($k) ? $name : $name;
            $mail->AddAttachment($item, $name);
        }
    }
    $arg = $mail->Send() ? TRUE : FALSE;
    if ($_REQUEST['test_model']) {
        echo '发信成功标志<hr />';
        var_dump($arg);
        _dump($mail);
    }
    return $arg;
}

/* function _exception($msg = '') {
$msg = empty($msg) ? 'debug info!' : $msg;
try {
throw new Exception($msg);
} catch (Exception $e) {
_dump($e->getTraceAsString());
}
} */

function _exception($msg = '', $exit = true){
    try {
        throw new Exception($msg);
    }
    catch (Exception $e) {
        foreach ($e->getTrace() as $k => $v) {
            $ee = '';
            $j  = '';
            if (!empty($v['args'])) {
                $t = '';
                foreach ($v['args'] as $a) {
                    $j .= $t . "'" . $a . "'";
                    $t = ',';
                }
            }
            if (!empty($v['class']) && !empty($v['function'])) {
                $ee .= ' ' . $v['class'] . $v['type'] . $v['function'] . '(' . $j . ')' . "\r\n";
            } elseif (empty($v['class']) && !empty($v['function'])) {
                $ee = ' ' . $v['function'] . '(' . $j . ')' . "\r\n";
            }
            $str .= '#' . $k . ' ' . $v['file'] . '(' . $v['line'] . '):' . $ee;
        }
        $str .= '#' . ($k + 1) . ' {main}';
        if ($exit) {
            _dump($str);
            exit();
        } else {
            return $str;
        }
    }
}

function call_excption($msg = '')
{
    return _exception($msg, false);
}

function arrayFormatting($areaList, $level)
{
    $areaList1 = $areaList2 = $areaList;
    $newArray  = Array();
    if (is_array($areaList)) {
        foreach ($areaList as $ak => $av) {
            if ($av[$level] == '1') {
                $newArray[] = $areaList[$ak];
            }
        }
    }
    foreach ($areaList2 as $k => $v) {
        foreach ($newArray as $nk => $nv) {
            if (isset($v['parentId']) && ($v['parentId'] == $nv['id'])) {
                $newArray[$nk]['son'][] = $areaList2[$k];
            }
        }
    }
    return $newArray;
}

/**
 * 调试数据 
 * @author feng.yin@baifendian.com
 * @date 2012-7-4
 */
function _dump()
{
    _set_header();
    echo '<pre>';
    foreach (func_get_args() as $item) {
        print_r($item);
        echo '<hr/>';
    }
    echo '<pre>';
}

/**
 * 替换空白
 * @param string $data
 * @author feng.yin@baifendian.com
 * @date 2012-7-4
 */
/* function replace_space($data) {
$a = array(
"/\\s(?=\\s)/i",
"/[\n\r\t]/",
);
$b = array(
'',
'',
);
return preg_replace($a, $b, $data);
} */

/**
 * 设置头部信息
 * @param string $char
 * @param string $type
 * @author feng.yin@baifendian.com
 * @date 2012-7-4
 */
function _set_header($char = '', $type = 'text/html')
{
    $char = 'utf-8';
    switch (trim($type)) {
        case 'javascript':
            @header("Content-Type:application/x-javascript;charset=$char");
            break;
        case 'json':
            @header("Content-Type:application/json;charset=$char");
            break;
        case 'xml':
            @header("Content-type: text/xml;charset=$char");
            break;
        case 'swf':
            @header("Content-type: application/x-shockwave-flash;");
            break;
        case 'gif':
            @header("Content-type:image/gif;");
            break;
        case 'jpg':
            @header("Content-type:image/jpg;");
            break;
        case 'png':
            @header("Content-type:image/png;");
            break;
        case 'css':
            @header("Content-type: text/css;charset=$char");
            break;
        default:
            @header("Content-type: text/html;charset=$char");
    }
}

function prev_day($day, $num = 1)
{
    if (is_numeric($day)) {
        $et = $day;
    } else {
        $et = strtotime($day);
    }
    return date("Y-m-d", ($et - (60 * 60 * 24 * $num)));
}

//下一天
function next_day($day, $num = 1)
{
    if (is_numeric($day)) {
        $et = $day;
    } else {
        $et = strtotime($day);
    }
    return date("Y-m-d", ($et + (60 * 60 * 24 * $num)));
}

function is_ip($gonten)
{
    $ips = explode(".", $gonten);
    if (empty($ips))
        return false;
    foreach ($ips as $ip) {
        if ($ip <= 0 || $ip > 255 || $ip == '')
            return false;
    }
    return ereg("^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$", $gonten);
}

/**
 * 下载数据
 * @param string|stream|etc $file_data 需要保存的文件
 * @param string $file_name 需要保存的文件名
 * @author feng.yin@baifendian.com
 * @date 2012-7-4
 */
function download($file_data, $file_name)
{
    @header('Content-type: application/octet-stream');
    @header('Accept-Ranges: bytes');
    $ua = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match("/MSIE/", $ua)) {
        $encoded_filename = urlencode($file_name);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $file_name . '"');
    } else {
        @header('Content-Disposition: attachment;filename="' . $file_name . '";');
    }
    @header('Accept-Length: ' . strlen($file_data));
    @header("Content-Transfer-Encoding: binary");
    header('Pragma: cache');
    header('Cache-Control: public, must-revalidate, max-age=0');
    @set_time_limit(0);
    echo $file_data;
    exit();
}

/**
 *  * 下载文件 
 * @param $file
 * @author feng.yin@baifendian.com
 * @date 2012-7-4
 */
function download_file($file, $file_name = '')
{
    @header('Content-type: application/octet-stream');
    @header('Accept-Ranges: bytes');
    $ua        = $_SERVER["HTTP_USER_AGENT"];
    $file_name = empty($file_name) ? basename($file) : $file_name;
    if (preg_match("/MSIE/", $ua)) {
        $encoded_filename = urlencode($file_name);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $file_name . '"');
    } else {
        @header('Content-Disposition: attachment;filename="' . $file_name . '";');
    }
    @header("Content-Transfer-Encoding: binary");
    @header("Content-Length: " . @filesize($file));
    header('Pragma: cache');
    header('Cache-Control: public, must-revalidate, max-age=0');
    @set_time_limit(0);
    @readfile($file);
}

/**
 * 获取当前URL路径
 * @author feng.yin@baifendian.com
 * @date 2012-7-4
 */
function get_curent_link($http = true)
{
    $port = $_SERVER['SERVER_PORT'];
    $path = $_SERVER['REQUEST_URI'];
    $path = empty($path) ? $_SERVER['QUERY_STRING'] : $path;
    $port = $_SERVER['SERVER_PORT'];
    $url  = $_SERVER['SERVER_NAME'] . ($port == 80 ? '' : $port) . '/' . $path;
    return ($http ? 'http://' : '') . str_replace('//', '/', $url);
}

/**
 * 转换为INT 默认取整数
 * @param $string
 * @param $ceil
 * @author feng.yin@baifendian.com
 * @date 2012-7-4* 
 */
function to_int($string, $ceil = true)
{
    if (empty($string))
        return 0;
    return $ceil ? ceil(abs(intval(trim($string)))) : abs(intval(trim($string)));
}

/**
 * @param array $array
 * @param object $class
 * @author feng.yin@baifendian.com
 * @date 2012-7-4
 */
function array_to_object($array, $class = 'stdClass')
{
    $array = !is_array($array) ? array(
        $array
    ) : $array;
    $obj   = new $class();
    foreach ($array as $k => $v) {
        $obj->$k = is_array($v) ? array_to_object($v, $class) : $v;
    }
    return $obj;
}

/**
 * 转换为浮点
 * @param $string
 * @author feng.yin@baifendian.com
 * @date 2012-7-4
 */
function float_int($string, $len = 2)
{
    if (!is_numeric($string))
        return 0.00;
    if (empty($string))
        return 0.00;
    return round(abs(floatval($string)), $len);
}

function stripslashes_deep($str)
{
    if (empty($str))
        return $str;
    return is_array($str) ? array_map('stripslashes_deep', $str) : stripslashes($str);
}

/**
 * 数组转换成json 和php的json转换不一样  但是转换出来的结果可以做json使用
 * @param array $arr 要转换的数组
 * @param boolean $fix 是否去掉 “ 符号
 * @return string 转换后的字符串
 * @author feng.yin
 * @date 2012-5-4上午09:30:13
 * @version V1R6B005
 */
function array_to_json($arr, $fix = true)
{
	$parts      = array();
    $is_list    = false;
    /* Find out if the given array is a numerical array */
    $keys       = array_keys($arr);
    $max_length = count($arr) - 1;
    /* See if the first key is 0 and last key is length - 1 */
    if (($keys['0'] == '0') && ($keys[$max_length] == $max_length)) {
        $is_list = true;
        /* See if each key correspondes to its position */
        for ($i = 0; $i < count($keys); $i++) {
            /* A key fails at position check. */
            if ($i != $keys[$i]) {
                /* It is an associative array. */
                $is_list = false;
                break;
            }
        }
    }
    foreach ($arr as $key => $value) {
        /* Custom handling for arrays */
        if (is_array($value)) {
            if ($is_list) {
                /* :RECURSION: */
                $parts[] = array_to_json($value, $fix);
            } else {
                /* :RECURSION: */
                $parts[] = ($fix ? '"' : '') . $key . ($fix ? '":' : ':') . array_to_json($value, $fix);
            }
        } else {
            $str = '';
            if (!$is_list)
                $str = ($fix ? '"' : '') . $key . ($fix ? '":' : ':');
            /* Custom handling for multiple data types */
            if (is_numeric($value))
                $str .= $value;
            /* Numbers */
            elseif ($value === false)
                $str .= 'false'; /* The booleans */ 
            elseif ($value === true)
                $str .= 'true';
            else
                $str .= '"' . addslashes($value) . '"';
            /* All other things */
            /* Is there any more datatype we should be in the lookout for? (Object?) */
            $parts[] = $str;
        }
    }
    $json = implode(',', $parts);
    if ($is_list)
        return '[' . str_replace(array(
            "\r\n"
        ), '', $json) . ']';
    /* Return numerical JSON */
    return '{' . str_replace(array(
        "\r\n"
    ), '', $json) . '}';
    /* Return associative JSON */
}

/**
 * 
 * JSON转换成array  若字符串中存在 {]=>等特殊字符的话就会被替换掉
 * @param string $jsonStr 要转换的字符串
 * @param boolean $obj 是否转换成对象
 * @return return_type
 * @author feng.yin
 * @date 2012-5-4上午09:31:37
 * @version V1R6B005
 */
function json_to_array($jsonStr, $obj = false)
{
    $str      = str_replace(array(
        ':',
        '{',
        '[',
        '}',
        ']'
    ), array(
        '=>',
        'array(',
        'array(',
        ')',
        ')'
    ), substr($jsonStr, 1, -1));
    $callback = @eval("return array({$str});");
    return $obj ? array_to_object($callback) : $callback;
}

/** 转义字符串 '转义为\' 包括key值一起转换
 * @param string|array|mix $array
 * @return mixed
 */
function addslashes_deep_all($string)
{
    if (empty($string))
        return $string;
    if (is_array($string)) {
        $temp = array();
        foreach ($string as $key => $item)
            $temp[!get_magic_quotes_gpc() ? addslashes(trim($key)) : trim($key)] = addslashes_deep_all($string);
    } else {
        return !get_magic_quotes_gpc() ? addslashes(trim($string)) : trim($string);
    }
}

/**
 * 截取字符串(支持UTF8和GBK编码),需要注意的是 GBK是2个字节一个汉字，UTF8是3 所有在截取字符长度的时候数字一定要是2或3的倍数
 * @param string $needCutString 需要截取的字符
 * @param int $end 需要截取的字符总数 默认为20个字符
 * @param int $start 开始位置 默认为0 可以执行截取的开始位置
 * @param string $endstring 附加字符 ...
 * @return string
 */
function truncate($needCutString = '', $end_length = 20, $start = 0, $endstring = '...')
{
    /*     * **********************************************************************
    1) 由于一个汉字占2个字符，因此起始位置为n不等于由第n个字符开始，需要先找到起始位置的字符数。
    2) while循环结束，就可得到起始位置的字符数
    * ********************************************************************** */
	$i      = 0;
    $output = "";
    while ($i < strlen($needCutString)) {
        // 循环执行的次数
        if ($start-- == 0) {
            break;
        }
        // 如果是汉字，字符数加2，否则字符数加1
        if (ord(substr($needCutString, $i, 1)) > 128) {
            $i++;
        }
        $i++;
    }
    // 截取长度，起始位置则是while循环结束时变量i的值
    for (; $i < strlen($needCutString); $i++) {
        // 汉字，取2个字符
        if (ord(substr($needCutString, $i, 1)) > 128) {
            $output .= substr($needCutString, $i, 2);
            $i++;
        } else {
            // 英文，取1个字符
            $output .= substr($needCutString, $i, 1);
        }
        // for循环结束标志，既可以是字符串尾部，也可以是截取长度。
        if (--$end_length == 0)
            break;
    }
    return empty($output) ? '' : $output . $endstring;
}
/**
 * GBK转化成UTF-8 此函数在KEY值为中文且为GBK编码时会出问题
 * @param strings $string
 */
function gbk_to_utf8($string)
{
    if (empty($string))
        return $string;
    return is_array($string) ? array_map("gbk_to_utf8", $string) : @iconv("GBK//IGNORE", "UTF-8", $string);
}

/**
 * UTF8转化成GBK 此函数在KEY值为中文且为UTF8编码时会出问题
 * @param string $string
 */
function utf8_to_gbk($string)
{
    if (empty($string))
        return $string;
    return is_array($string) ? array_map("utf8_to_gbk", $string) : @iconv("UTF-8", "GBK", $string);
}

/**
 * 序列化数据使用
 * @param string|array $data
 * @return string
 */
function serialize_deep($data)
{
    return addslashes(serialize(stripslashes_deep($data)));
}

/**
 * 递归反序列化数据
 * @param string||array $data
 * @return mixed
 */
function unserialize_deep($data)
{
    if (empty($data))
        return $data;
    return is_array($data) ? array_map("unserialize_deep", $data) : unserialize($data);
}

/**
 * 获取数组值 并强制转换成 INT
 * @param array $array
 * @param string $key
 * @param boolean $join
 */
function get_array_value_for_int($array, $key = '', $join = false, $uniq = false, $filter = true, $int = true, $split_arg = ',')
{
    if (empty($array))
        return null;
    $array = !is_array($array) ? array_filter(explode($split_arg, $array)) : (array) $array;
    $res   = array();
    foreach ($array as $a) {
        $val   = empty($key) ? $a : $a[$key];
        $res[] = $int ? to_int($val) : $val;
    }
    if ($filter) {
        $res = array_filter($res);
    }
    $res = $uniq ? array_unique($res) : $res;
    return $join ? (empty($res)?0:join(',', $res)) : $res;
}

if (!function_exists('get_array_value')) {
    function get_array_value($array, $key = 'id', $fixed_key = false)
    {
        if (!$array || !is_array($array))
            return array();
        $res = array();
        foreach ($array as $k => $a) {
            if ($fixed_key) {
                $res[$k] = $a[$key];
            } else {
                $res[] = $a[$key];
            }
        }
        return $res;
    }
    
}

function get_array_keys($data, $show_key = array())
{
    $show_key = (array) $show_key;
    foreach ($show_key as $s) {
        if (in_array($s, array_keys($data))) {
            $r[$s] = $data[$s];
        }
    }
    return $r;
}

function string_to_int($ids)
{
    $temp = explode(',', $ids);
    foreach ($temp as &$i)
        $i = to_int($i);
    $temp = array_filter($temp);
    return empty($temp) ? 0 : join(',', $temp);
}

function array_combine_special($a, $b, $pad = false)
{
    $acount = count($a);
    $bcount = count($b);
    if (!$pad) {
        $size = ($acount > $bcount) ? $bcount : $acount;
        $a    = array_slice($a, 0, $size);
        $b    = array_slice($b, 0, $size);
    } else {
        if ($acount > $bcount) {
            $more = $acount - $bcount;
            $more = $acount - $bcount;
            for ($i = 0; $i < $more; $i++) {
                $b[] = "";
            }
        } else if ($acount < $bcount) {
            $more = $bcount - $acount;
            for ($i = 0; $i < $more; $i++) {
                $key = 'extend_filed_0' . $i;
                $a[] = $key;
            }
        }
    }
    return array_combine($a, $b);
}
/**
 * 数组分组
 * @param array $arr
 * @param string $key_field
 */
function array_group_by($arr, $key_field, $mulit = true)
{
	$ret = array();
	foreach ($arr as $row) {
		$key = $row[$key_field];
		if ($mulit) {
			$ret[$key][] = $row;
		} else {
			$ret[$key] = $row;
		}
	}
	return $ret;
}
function create_file($data, $file, $cover = "wb+", $mod = 0777) //ab追加 WB 覆盖
{
    @mkdirs_a(dirname($file));
    $fp = fopen($file, $cover);
    fwrite($fp, $data); //="\xEF\xBB\xBF".$text;
    @flock($fp, LOCK_EX);
    @fclose($fp);
    @chmod($file, $mod);
    return !is_file($file) ? false : true;
}

/**
 * 删除指定文件夹 默认删除自身
 */
function delete_folder($dir, $delete_self = true)
{
    $folder_list = array(
        $dir
    );
    $delete_self ? $temp[] = $dir : $temp = array();
    while (true) {
        if (!$folder_list)
            break;
        $_this_dir = array_pop($folder_list);
        $hd        = opendir($_this_dir);
        while (false != $_this_file = readdir($hd)) {
            if ($_this_file == '.' || $_this_file == '..')
                continue;
            $path = $_this_dir . '/' . $_this_file;
            if (is_dir($path)) {
                $folder_list[] = $path;
                $temp[]        = $path;
            }
            @unlink($path);
        }
        closedir($hd);
    }
    foreach (array_reverse($temp) as $item)
        @rmdir($item);
    unset($temp, $folder_list, $dir);
    return true;
}

function mkdirs_a($path, $mode = 0777)
{
    $path      = str_replace("\\", '/', $path);
    $dirs      = explode('/', ($path = substr($path, -1, 1) !== '/' ? $path . '/' : $path));
    $pos       = strrpos($path, ".");
    $subamount = $pos === false ? 0 : 1;
    for ($c = 0; $c < count($dirs) - $subamount; $c++) {
        $thispath = "";
        for ($cc = 0; $cc <= $c; $cc++) {
            $thispath .= $dirs[$cc] . '/';
        }
        if (!file_exists($thispath))
            @mkdir($thispath, $mode);
    }
}

/**
 * SQL处理
 * @param array $where
 * @return return_type
 * @author feng.yin
 * @date 2012-4-16上午10:29:35
 * @version V1R6B005
 */
function _do_sql_where($where)
{
    $comma = $info = '';
    if (is_array($where) && count($where)) {
        foreach ($where as $key => $value) {
            if (strstr($value, 'IN') || strstr($value, '!=') || strstr($value, 'NOT IN') || strstr($value, '<=') || strstr($value, '>=') || strstr($value, '<') || strstr($value, '>') || strstr($value, 'LIKE') || strstr($value, 'OR')) {
                $info .= "$comma`$key` $value ";
            } else {
                $info .= "$comma`$key`" . ' = ' . "'" . $value . "'";
            }
            $comma = ' AND ';
        }
    }
    return $info;
}

/**
 * SQL的UPDATE操作
 * @param array $data
 * @param string $table
 * @param array $where
 * @return return_type
 * @author feng.yin
 * @date 2012-4-16上午09:40:35
 * @version V1R6B005
 */
function do_sql_update($data, $table, $where = '', $do = true)
{
    if (!is_array($data) || empty($data) || empty($table)) {
    	return false;
    }
    $arg   = C('DB_PREFIX_REPLACE_KEY');
    $table = !strstr($table, $arg) ? $arg . $table : $table;
    $value = _do_sql_update_value($data);
    unset($data);
    $where = empty($where) ? ' ' : ' WHERE ' . _do_sql_where($where); //构造where字子句
    $sql   = "UPDATE `$table` SET $value $where;";
    
    unset($data, $where);
    
    return $do ? D()->execute($sql) : $sql;
}

function _do_sql_update_value($array)
{
    $comma = $info = '';
    if (is_array($array) && count($array)) {
        foreach ($array as $key => $value) {
            $info .= "$comma`$key`" . ' = ' . "'$value'";
            $comma = ', ';
        }
    }
    return $info;
}

function _insert_filed($array, $loadfiled = true)
{
    if (!is_array($array))
        return $array;
    $tag = $field = $value = '';
    foreach ($array as $key => $v) {
        $field .= "$tag`" . $key . "`";
        $value .= "$tag'" . $v . "'";
        $tag = ' , ';
    }
    return $loadfiled ? $field : $value;
}

function _to_insert($array)
{
    if (!is_array($array))
        return FALSE;
    $result = '';
    if (!is_mulit_array($array)) {
        $result = '(' . _insert_filed($array) . ') VALUES (' . _insert_filed($array, FALSE) . ');';
    } else {
        $tag          = '';
        $first        = '';
        $curent_array = current($array);
        $result .= '(' . _insert_filed($curent_array) . ') VALUES ' . "\n" . '(';
        foreach ($array as $values) {
            $result .= $tag . $first . _insert_filed($values, FALSE);
            $tag   = '),';
            $first = '(';
        }
        $result .= ');';
    }
    unset($curent_array, $array);
    return $result;
}

/**
 * SQL的插入操作
 */
function do_sql_insert($data = array(), $table, $insert = true)
{
    if (empty($data) || empty($table))
        return false;
    $arg   = C('DB_PREFIX_REPLACE_KEY');
    $table = !strstr($table, $arg) ? $arg . $table : $table;
    $va    = _to_insert($data);
    unset($data);
    $sql = "INSERT INTO " . "`$table` $va";
    if($insert){
    	$obj = D();
    	return $obj->execute($sql)?$obj->getLastInsID():0;
    }else{
    	return $sql;
    }
   /*  return $insert ? D()->execute($sql) : $sql; */
}

/**
 * 判断是否是post 
 */
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
}

/**
 * 判断是否是get 
 */
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
}

/**
 * 判断是否是AJAX 
 */
function is_ajax_call()
{
    return strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? true : false;
}

/**
 * 把一个多维array处理成一个一维array
 *
 * @param array $array 需要处理的array
 * @param boolen $sort 是否保持键值
 * @return array
 */
function mulit_ary_to_array($data)
{
    $temp = array();
    foreach ($data as $n) {
        $temp = array_merge($temp, $n);
    }
    return array_unique($temp);
}

/**
 * 合并两个数据相同的数据 二维数组
 * @param array $array
 * @param boolean $keep_key_assoc
 * @return return_type
 * @author feng.yin
 * @date 2012-5-4上午09:29:16
 * @version V1R6B005
 */
function array_uniques($array, $keep_key_assoc = false)
{
    $duplicate_keys = array();
    $tmp            = array();
    foreach ($array as $key => $val) {
        if (is_object($val)) {
            $val = (array) $val;
        }
        if (!in_array($val, $tmp)) {
            $tmp[] = $val;
        } else {
            $duplicate_keys[] = $key;
        }
    }
    foreach ($duplicate_keys as $key) {
        unset($array[$key]);
    }
    return $keep_key_assoc ? $array : array_values($array);
}

/**
 * 直提取缓存
 * @param string $key 缓存提取键值
 * @param string $call_function  缓存提取的方法 使用 call_user_func_array 方式获取
 * @param array $call_function_args 传递的参数
 */
function cache_load($key, $call_function, $call_function_args = array(), $exp = '')
{
    if (empty($key))
        return array();
    $cache = Cache::getInstance();
    $data  = (array) $GLOBALS[$key];
    if (!$data) {
        $data = $cache->get($key);
        if (!$data) {
            $data = call_user_func_array($call_function, $call_function_args);
           // $data = !$data ? call_user_func($call_function, $call_function_args) : $data;
            $cache->set($key, $data, $exp);
        }
    }
    return $data;
}

/**
 * 
 * 删除缓存
 * @param string $cache_key
 * @return return_type
 * @author feng.yin
 * @date 2012-6-15上午09:47:54
 * @version V1R6B005
 */
function cache_remove($cache_key)
{
    return Cache::getInstance()->rm($cache_key);
}

/**
 * html编码
 * @param unknown_type $array
 * @return return_type
 * @author feng.yin
 * @date 2012-6-18下午03:41:39
 * @version V1R6B005
 */
function html_encode($array)
{
    if (empty($array))
        return $array;
    return is_array($array) ? array_map('html_encode', $array) : addslashes(stripslashes(str_replace(array(
        '\&quot',
        '\&#039;',
        '\\'
    ), array(
        '&quot',
        '&#039;',
        ''
    ), trim(htmlspecialchars($array, ENT_QUOTES)))));
}

/**
 * html解码
 * @param unknown_type $array
 * @return return_type
 * @author feng.yin
 * @date 2012-6-18下午03:41:26
 * @version V1R6B005
 */
function html_decode($array)
{
    if (empty($array))
        return $array;
    return is_array($array) ? array_map("html_decode", $array) : htmlspecialchars_decode($array, ENT_QUOTES);
}

function curl_get_contents($path)
{
    $ch = curl_init(); //初始化一个curl
    curl_setopt($ch, CURLOPT_URL, $path); //设置要请求的文件地址
    curl_setopt($ch, CURLOPT_TIMEOUT, "240"); //设置超时时间
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //设置是将获取的数据返回而不输出
    $res = curl_exec($ch); //执行这个curl
    curl_close($ch); //关闭curl
    return $res;
}

function is_mulit_array($array)
{
    if (!is_array($array) || empty($array))
        return FALSE;
    foreach ($array as $item) {
        if (is_array($item)) {
            return true;
        }
    }
    return FALSE;
}

/**
 * 
 * 获取分页计算数据
 * @param unknown_type $total
 * @param unknown_type $perpage
 * @return return_type
 * @author feng.yin
 * @date 2012-4-6上午11:55:58
 * @version V1R6B005
 */
function parse_page($total, $perpage, $curpage)
{
    /* 每页显示的数量 */
    $perpage           = to_int($perpage);
    $perpage           = empty($perpage) ? 10 : $perpage;
    $pages             = @abs(ceil($total / $perpage));
    $curpage           = $curpage > $pages ? $pages : max(to_int($curpage), 1);
    $curent_select_num = ($curpage - 1) * $perpage;
    $curent_select_num = $curent_select_num <= 0 ? 0 : $curent_select_num;
    return array(
        'total' => $total,
        'perpage' => $perpage,
        'curpage' => $curpage,
        'total_page' => $pages,
        'limit' => array(
            'start' => $curent_select_num,
            'end' => $perpage
        )
    );
}

/**
 * 自定义加密函数
 *
 * @param string $string 需要加密的数据
 * @param string $operation 加密开关
 * @param string $key 自定义字符串
 * @return string
 */
function authcode($string, $operation, $key = '') {
	if(empty($key)){
		$key = C('AUTH_HASH_KEY');
		if(empty($key))$key = 'sdl@$^&(8799809fjksaldASFGd045df45dfgkdfgsdf%&()dlfgdjasS$^&*klsdDFLSKDLFSDKA$^&&^()_)3554ksdflSJFDSKDFKSDk';
	}
	$key = md5($key);
	$key_length = strlen($key);
	$string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 16).$string;
	$string_length = strlen($string);
	$rndkey = $box = array();
	$result = '';
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($key[$i % $key_length]);
		$box[$i] = $i;
	}
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		if(substr($result, 0, 16) == substr(md5(substr($result, 16).$key), 0, 16)) {
			return substr($result, 16);
		} else {
			return '';
		}
	} else {
		return str_replace('=', '', base64_encode($result));
	}
}
/**
 * base64转码加密函数
 * @param string $str  要加密的字符
 * @param  string $key 自定义的加密字符串-追加
 * @return string
 */
function string_encode($str,$key = ''){
	return base64_encode(authcode($str,'ENCODE',$key));
}
/**
 * base64反解密加密函数
 * @param string $str 要解密的字符
 * @param string $key 自定义的加密字符串-追加
 * @return string
 */
function string_decode($str,$key = ''){
	return authcode(base64_decode($str),'DECODE',$key);
}
/**
 * 写数据到队列池
 * @param array $data
 * @param string  $type mail/sms
 */
/* function to_insert_quee_msg($data,$type='mail'){
	do_sql_insert(array(
		'content'=>serialize_deep($data),
		'time'=>time(),
		'type'=>$type,
	),'message_queue');
}
 */
function do_syn_use_sock($link, $append_data = array(), $host = ''){
	$host_auto   = C('call_api_curl_link');
	if (empty($host_auto)) {
		$host_auto = $_SERVER['HTTP_HOST'];
	}
	$host = empty($host) ? $host_auto : $host;
	$fp = fsockopen($host, 80, $errno, $errstr, 30);
	if (!$fp) {
		create_file(var_export(array(
		'msg' => $errno,
		'callhost'=>$host,
		'errstr'=>$errstr,
		'time' => date("YmdHis"),
		'request' => $_REQUEST,
		'call_arg'=>func_get_args(),
		), true) . "\r\n", SITE_ROOT . 'log/curl_call_error.log', 'ab+');
		return false;
	}
	stream_set_blocking($fp,0);
	$encoded = '';
	while (false!=list($k,$v) = each($append_data)) {
		$encoded .= ($encoded ? "&" : "");
		$encoded .= rawurlencode($k)."=".rawurlencode($v);
	}
	$links = $link.'?'.$encoded;
	/**
	 POST /Home/findpwd/ HTTP/1.1
	 Host: demo.lulu.com
	 User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:31.0) Gecko/20100101 Firefox/31.0
	 Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3
	 Accept-Encoding: gzip, deflate
	 Content-Type: application/x-www-form-urlencoded; charset=UTF-8
	 X-Requested-With: XMLHttpRequest
	 Referer: http://demo.lulu.com/Home/findpwd/
	 Content-Length: 55
	 Cookie: __APP_PUB__=13aaff2a9q8n1uekp0dsavv997
	 Connection: keep-alive
	 Pragma: no-cache
	 Cache-Control: no-cache
	 */
	$http = "POST $link HTTP/1.1\n";
	$http .= "Host: $host\n";
	$http.= "Content-type: application/x-www-form-urlencoded\n";
	//$http.="Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
	$http .="Content-length: " . strlen($encoded) . "\n";
	$http .="Connection: close\n\n";
	$http .="$encoded\n";
	//$http .= "Connection: Close\r\n\r\n";
	fwrite($fp,$http);
	fclose($fp);
}
//异步执行
function do_syn($link, $append_data = array(), $host = ''){
	return do_syn_use_sock($link,$append_data,$host);
	/*
    $host_auto   = C('call_api_curl_link');
    if (empty($host_auto)) {
        $host_auto = $_SERVER['HTTP_HOST'];
    }
    $host = empty($host) ? $host_auto : $host;
    $host = substr($host, -1, 1) != '/' ? $host : substr($host, 0, -1);
    $host = 'http://' . $host . $link;
	$append_data = http_build_query($append_data);
	$ch          = curl_init();
	curl_setopt($ch, CURLOPT_URL, $host);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	//curl_setopt ( $ch,  CURLOPT_NOSIGNAL, true);
	//curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200); //毫秒
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //设置是将获取的数据返回而不显示
	curl_setopt($ch, CURLOPT_POSTFIELDS, $append_data);
	$res = trim(curl_exec($ch));
	$curl_error= curl_errno($ch);
	if (!empty($curl_error)) {
		create_file(var_export(array(
		'msg' => $curl_error,
		'callhost'=>$host,
		'time' => date("YmdHis"),
		//'file' => get_included_files(),
		'request' => $_REQUEST,
		'call_arg'=>func_get_args(),
		), true) . "\r\n", SITE_ROOT . 'log/curl_call_error.log', 'ab+');
	}
	curl_close($ch);
	create_file(var_export(array(
		'callback_data' => $res,
		'callhost'=>$host,
		'time' => date("YmdHis"),
		'request' => $_REQUEST,
		'call_arg'=>func_get_args(),
		), true) . "\r\n", SITE_ROOT . 'log/curl_call_back_data.log', 'ab+');
	
	return  trim($res);
	*/
/**
错误代码列表

CURLE_UNSUPPORTED_PROTOCOL (1) – 您传送给 libcurl 的网址使用了此 libcurl 不支持的协议。 可能是您没有使用的编译时选项造成了这种情况（可能是协议字符串拼写有误，或没有指定协议 libcurl 代码）。

CURLE_FAILED_INIT (2) – 非常早期的初始化代码失败。 可能是内部错误或问题。

CURLE_URL_MALFORMAT (3) – 网址格式不正确。

CURLE_COULDNT_RESOLVE_PROXY (5) – 无法解析代理服务器。 指定的代理服务器主机无法解析。

CURLE_COULDNT_RESOLVE_HOST (6) – 无法解析主机。 指定的远程主机无法解析。

CURLE_COULDNT_CONNECT (7) – 无法通过 connect() 连接至主机或代理服务器。

CURLE_FTP_WEIRD_SERVER_REPLY (8) – 在连接到 FTP 服务器后，libcurl 需要收到特定的回复。 此错误代码表示收到了不正常或不正确的回复。 指定的远程服务器可能不是正确的 FTP 服务器。

CURLE_REMOTE_ACCESS_DENIED (9) – 我们无法访问网址中指定的资源。 对于 FTP，如果尝试更改为远程目录，就会发生这种情况。

CURLE_FTP_WEIRD_PASS_REPLY (11) – 在将 FTP 密码发送到服务器后，libcurl 需要收到正确的回复。 此错误代码表示返回的是意外的代码。

CURLE_FTP_WEIRD_PASV_REPLY (13) – libcurl 无法从服务器端收到有用的结果，作为对 PASV 或 EPSV 命令的响应。 服务器有问题。

CURLE_FTP_WEIRD_227_FORMAT (14) – FTP 服务器返回 227 行作为对 PASV 命令的响应。如果 libcurl 无法解析此行，就会返回此代码。

CURLE_FTP_CANT_GET_HOST (15) – 在查找用于新连接的主机时出现内部错误。

CURLE_FTP_COULDNT_SET_TYPE (17) – 在尝试将传输模式设置为二进制或 ascii 时发生错误。

CURLE_PARTIAL_FILE (18) – 文件传输尺寸小于或大于预期。当服务器先报告了一个预期的传输尺寸，然后所传送的数据与先前指定尺寸不相符时，就会发生此错误。

CURLE_FTP_COULDNT_RETR_FILE (19) – ‘RETR’ 命令收到了不正常的回复，或完成的传输尺寸为零字节。

CURLE_QUOTE_ERROR (21) – 在向远程服务器发送自定义 “QUOTE” 命令时，其中一个命令返回的错误代码为 400 或更大的数字（对于 FTP），或以其他方式表明命令无法成功完成。

CURLE_HTTP_RETURNED_ERROR (22) – 如果 CURLOPT_FAILONERROR 设置为 TRUE，且 HTTP 服务器返回 >= 400 的错误代码，就会返回此代码。 （此错误代码以前又称为 CURLE_HTTP_NOT_FOUND。）

CURLE_WRITE_ERROR (23) – 在向本地文件写入所收到的数据时发生错误，或由写入回调 (write callback) 向 libcurl 返回了一个错误。

CURLE_UPLOAD_FAILED (25) – 无法开始上传。 对于 FTP，服务器通常会拒绝执行 STOR 命令。错误缓冲区通常会提供服务器对此问题的说明。 （此错误代码以前又称为 CURLE_FTP_COULDNT_STOR_FILE。）

CURLE_READ_ERROR (26) – 读取本地文件时遇到问题，或由读取回调 (read callback) 返回了一个错误。

CURLE_OUT_OF_MEMORY (27) – 内存分配请求失败。此错误比较严重，若发生此错误，则表明出现了非常严重的问题。

CURLE_OPERATION_TIMEDOUT (28) – 操作超时。 已达到根据相应情况指定的超时时间。 请注意： 自 Urchin 6.6.0.2 开始，超时时间可以自行更改。 要指定远程日志下载超时，请打开 urchin.conf 文件，取消以下行的注释标记：

#DownloadTimeout: 30 

CURLE_FTP_PORT_FAILED (30) – FTP PORT 命令返回错误。 在没有为 libcurl 指定适当的地址使用时，最有可能发生此问题。 请参阅 CURLOPT_FTPPORT。

CURLE_FTP_COULDNT_USE_REST (31) – FTP REST 命令返回错误。如果服务器正常，则应当不会发生这种情况。

CURLE_RANGE_ERROR (33) – 服务器不支持或不接受范围请求。

CURLE_HTTP_POST_ERROR (34) – 此问题比较少见，主要由内部混乱引发。

CURLE_SSL_CONNECT_ERROR (35) – 同时使用 SSL/TLS 时可能会发生此错误。您可以访问错误缓冲区查看相应信息，其中会对此问题进行更详细的介绍。可能是证书（文件格式、路径、许可）、密码及其他因素导致了此问题。

CURLE_FTP_BAD_DOWNLOAD_RESUME (36) – 尝试恢复超过文件大小限制的 FTP 连接。

CURLE_FILE_COULDNT_READ_FILE (37) – 无法打开 FILE:// 路径下的文件。原因很可能是文件路径无法识别现有文件。 建议您检查文件的访问权限。

CURLE_LDAP_CANNOT_BIND (38) – LDAP 无法绑定。LDAP 绑定操作失败。

CURLE_LDAP_SEARCH_FAILED (39) – LDAP 搜索无法进行。

CURLE_FUNCTION_NOT_FOUND (41) – 找不到函数。 找不到必要的 zlib 函数。

CURLE_ABORTED_BY_CALLBACK (42) – 由回调中止。 回调向 libcurl 返回了 “abort”。

CURLE_BAD_FUNCTION_ARGUMENT (43) – 内部错误。 使用了不正确的参数调用函数。

CURLE_INTERFACE_FAILED (45) – 界面错误。 指定的外部界面无法使用。 请通过 CURLOPT_INTERFACE 设置要使用哪个界面来处理外部连接的来源 IP 地址。 （此错误代码以前又称为 CURLE_HTTP_PORT_FAILED。）

CURLE_TOO_MANY_REDIRECTS (47) – 重定向过多。 进行重定向时，libcurl 达到了网页点击上限。请使用 CURLOPT_MAXREDIRS 设置上限。

CURLE_UNKNOWN_TELNET_OPTION (48) – 无法识别以 CURLOPT_TELNETOPTIONS 设置的选项。 请参阅相关文档。

CURLE_TELNET_OPTION_SYNTAX (49) – telnet 选项字符串的格式不正确。

CURLE_PEER_FAILED_VERIFICATION (51) – 远程服务器的 SSL 证书或 SSH md5 指纹不正确。

CURLE_GOT_NOTHING (52) – 服务器未返回任何数据，在相应情况下，未返回任何数据就属于出现错误。

CURLE_SSL_ENGINE_NOTFOUND (53) – 找不到指定的加密引擎。

CURLE_SSL_ENGINE_SETFAILED (54) – 无法将选定的 SSL 加密引擎设为默认选项。

CURLE_SEND_ERROR (55) – 无法发送网络数据。

CURLE_RECV_ERROR (56) – 接收网络数据失败。

CURLE_SSL_CERTPROBLEM (58) – 本地客户端证书有问题

CURLE_SSL_CIPHER (59) – 无法使用指定的密钥

CURLE_SSL_CACERT (60) – 无法使用已知的 CA 证书验证对等证书

CURLE_BAD_CONTENT_ENCODING (61) – 无法识别传输编码

CURLE_LDAP_INVALID_URL (62) – LDAP 网址无效

CURLE_FILESIZE_EXCEEDED (63) – 超过了文件大小上限

CURLE_USE_SSL_FAILED (64) – 请求的 FTP SSL 级别失败

CURLE_SEND_FAIL_REWIND (65) – 进行发送操作时，curl 必须回转数据以便重新传输，但回转操作未能成功

CURLE_SSL_ENGINE_INITFAILED (66) – SSL 引擎初始化失败

CURLE_LOGIN_DENIED (67) – 远程服务器拒绝 curl 登录（7.13.1 新增功能）

CURLE_TFTP_NOTFOUND (68) – 在 TFTP 服务器上找不到文件

CURLE_TFTP_PERM (69) – 在 TFTP 服务器上遇到权限问题

CURLE_REMOTE_DISK_FULL (70) – 服务器磁盘空间不足

CURLE_TFTP_ILLEGAL (71) – TFTP 操作非法

CURLE_TFTP_UNKNOWNID (72) – TFTP 传输 ID 未知

CURLE_REMOTE_FILE_EXISTS (73) – 文件已存在，无法覆盖

CURLE_TFTP_NOSUCHUSER (74) – 运行正常的 TFTP 服务器不会返回此错误

CURLE_CONV_FAILED (75) – 字符转换失败

CURLE_CONV_REQD (76) – 调用方必须注册转换回调

CURLE_SSL_CACERT_BADFILE (77) – 读取 SSL CA 证书时遇到问题（可能是路径错误或访问权限问题）

CURLE_REMOTE_FILE_NOT_FOUND (78) – 网址中引用的资源不存在

CURLE_SSH (79) – SSH 会话中发生无法识别的错误

CURLE_SSL_SHUTDOWN_FAILED (80) – 无法终止 SSL 连接
 */

}
function __e($lang)
{
    echo __r($lang);
}

function __r($string)
{
    _init_lang();
    $mo2po = new gettext_reader(new CachedFileReader(LANG_FILE));
    return $mo2po->translate($string);
}

function _init_lang()
{
    $lang   = C('DEFAULT_LANG');
    //$lang = isset($_GET['lang']) ? $_GET['lang'] : 'zh';
    $mofile = SITE_ROOT . 'lang/' . $lang . '/lang.mo';
    if (!defined('LANG_FILE')) {
        define('LANG_FILE', $mofile);
    }
    import('@.ORG.lang');
}

function get_ip()
{
    static $realip = NULL;
    if ($realip !== NULL)
        return $realip;
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($arr AS $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    $onlineip = array();
    preg_match("/[\\d\\.]{7,15}/", $realip, $onlineip);
    return !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
}

/**
 * 执行PHP
 * @param $content
 *
 */
function _eval($content)
{
    return eval('?>' . trim($content));
}

/**
 * <code>
 * $data = array('name'=>'test','ary'=>array('test'=>'test key'));
 * _find_value($data,'name.ary.test'); 返回 test key
 * </code>
 * 通过字符串查找数据里的数据
 * @param array $data
 * @param string $arg
 */
function find_value($data, $arg)
{
    if (empty($arg))
        return $data;
    $GLOBALS['__bfp_temp_val_data__'] = $data;
    $call                             = explode('.', $arg);
    $string                           = '';
    foreach ($call as $item)
        $string .= "['" . $item . "']";
    $code = '<?php return $GLOBALS[\'__bfp_temp_val_data__\']' . $string . ' ; ?>';
    $res  = _eval($code);
    unset($GLOBALS["__bfp_temp_val_data__"]);
    return $res;
}

function header_location($url)
{
    @header("Location:$url");
}

function js_location($url)
{
    $str = '<script type="text/javascript">
	window.onload = function(){
		window.setTimeout(function(){
			window.location.href="%s";
		},100);
	};
	</script>';
    echo sprintf($str, $url);
    exit();
}

function back_location()
{
    echo "<script type=\"text/javascript\">window.history.go(-1);</script>";
    exit;
}

function array_to_tree($arr, $fid = 'id', $fparent = 'parent_id', $fchildrens = 'childrens', $returnReferences = false)
{
    $pkvRefs = array();
    foreach ($arr as $offset => $row)
        $pkvRefs[$row[$fid]] =& $arr[$offset];
    $tree = array();
    foreach ($arr as $offset => $row) {
        $parentId = $row[$fparent];
        if ($parentId) {
            if (!isset($pkvRefs[$parentId])) {
                continue;
            }
            $parent =& $pkvRefs[$parentId];
            $parent[$fchildrens][] =& $arr[$offset];
        } else {
            $tree[] =& $arr[$offset];
        }
    }
    if ($returnReferences) {
        return array(
            'tree' => $tree,
            'analyze_data' => $pkvRefs
        );
    } else {
        return $tree;
    }
}
function __get_tree_from_array($array, $primary = 'id', $foreign = 'pid', $parent = 0, $level = 10, $showKey = '')
{
    if (empty($array) || !is_array($array))
        return array();
    $parent = max(0, (int) $parent);
    $level  = max(1, (int) $level);
    if (is_string($showKey) && !empty($showKey))
        $showKey = array(
            $showKey
        );
    $children = array();
    $links    = array();
    $root     = array();
    foreach ($array as $row) {
        if ($row[$foreign] == $parent) {
            $root[] = $row[$primary];
            /* 处理自定义字段 */
            if (!empty($showKey) && is_array($showKey)) {
                foreach ($showKey as $s)
                    if (in_array($s, array_keys($row)))
                        $r[$s] = $row[$s];
                $children[] = $r;
            } else {
                $row['level'] = 1;
                $children[]   = $row;
            }
            if ($level > 1)
                $links[$row[$primary]] = count($children) - 1;
        } elseif (in_array($row[$foreign], array_keys($links))) {
            $link = '';
            $temp = $row[$foreign];
            while (!in_array($temp, $root)) {
                $link = ',' . $temp . $link;
                $temp = $links[$temp];
            }
            $link  = $links[$temp] . $link;
            $link  = explode(',', $link);
            $curlv = count($link) + 1;
            $ary =& $children;
            foreach ($link as $item) {
                if (!isset($ary[$item]['childrens']))
                    $ary[$item]['childrens'] = array();
                $ary =& $ary[$item]['childrens'];
            }
            /* 处理自定义字段 */
            $filter = $row;
            if (!empty($showKey) && is_array($showKey)) {
                foreach ($showKey as $s) {
                    if (in_array($s, array_keys($filter)))
                        $r[$s] = $row[$s];
                }
                $ary[$row[$primary]] = $r;
            } else {
                $ary[$row[$primary]] = array_merge($filter, array(
                    'level' => $curlv
                ));
            }
            if ($curlv < $level)
                $links[$row[$primary]] = $row[$foreign];
        }
    }
    return $children;
}

/**
 * 数组排序
 * @param unknown_type $array
 * @param unknown_type $keyname
 * @param unknown_type $sortDirection
 * @return return_type
 * @author feng.yin
 * @date 2012-5-8下午03:59:44
 * @version V1R6B005
 */
function array_column_sort($array, $keyname, $sortDirection = SORT_ASC)
{
    return __array_sortby_multifields($array, array(
        $keyname => $sortDirection
    ));
}

function __array_sortby_multifields($rowset, $args)
{
    $sortArray = array();
    $sortRule  = '';
    foreach ($args as $sortField => $sortDir) {
        foreach ($rowset as $offset => $row) {
            @$sortArray[$sortField][$offset] = $row[$sortField];
        }
        $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
    }
    if (empty($sortArray) || empty($sortRule)) {
        return $rowset;
    }
    eval('array_multisort(' . $sortRule . '$rowset);');
    return $rowset;
}

/**
 * 得到树的子节点
 * @param unknown_type $tree
 * @param unknown_type $parent_id
 * @param unknown_type $son_item
 * @param unknown_type $parent_item
 * @param unknown_type $all
 * @param unknown_type $showKey
 * @param unknown_type $result
 * @return multitype:|Ambigous <multitype:, unknown>
 * @author feng.yin@baifendian.com
 * @date 2012-10-13下午3:55:38
 */
function get_tree_son($tree, $parent_id = 0, $son_item = 'id', $parent_item = 'pid', $all = true, $showKey = '', &$result = array())
{
    if (!is_array($tree) || empty($tree))
        return array();
    foreach ($tree as $row) {
        if ($row[$parent_item] == $parent_id) {
            $result[] = __self_key($row, $showKey);
            if ($all)
                get_tree_son($tree, $row[$son_item], $son_item, $parent_item, $all, $showKey, $result);
        }
    }
    return $result;
}

/**
 * 
 * @param unknown_type $tree
 * @param unknown_type $find_parent_id
 * @param unknown_type $son_item
 * @param unknown_type $parent_item
 * @param unknown_type $showKey
 * @param unknown_type $showall
 * @param unknown_type $result
 * @return multitype:
 * @author feng.yin@baifendian.com
 * @date 2012-10-13下午3:55:30
 */
function get_tree_parent($tree, $find_parent_id, $son_item = 'id', $parent_item = 'pid', $showKey = '', $showall = true, &$result = array())
{
    if (!is_array($tree) || empty($tree))
        return array();
    foreach ($tree as $row) {
        if ($row[$son_item] == $find_parent_id) {
            $result[] = __self_key($row, $showKey);
            if ($showall)
                get_tree_parent($tree, $row[$parent_item], $son_item, $parent_item, $showKey, $showall, $result);
        }
    }
    return array_reverse($result);
}

function __self_key($row, $showKey)
{
    if (is_string($showKey) && !empty($showKey))
        $showKey = array(
            $showKey
        );
    $result = array();
    if (!empty($showKey) && is_array($showKey) && count($showKey) > 1) {
        foreach ($showKey as $s) {
            if (in_array($s, array_keys($row))) {
                $r[$s] = $row[$s];
            }
        }
        $result = $r;
    } elseif (!empty($showKey) && is_array($showKey) && count($showKey) == 1) {
        $result = $row[$showKey[0]];
    } else {
        $result = $row;
    }
    return $result;
}

function get_data_from_rc($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    return $data;
}

//十进制转换成8为16进制
function todechex($num, $zero = 8)
{
    // $num = dechex($num);
    // while (strlen($num) < $zero) {
    //     $num = "0" . $num;
    // }
    return sprintf("%0{$zero}x", $num);
    //return $num;
}

/**
 * 获取网站地址(获得当前访问地址的URL)有多少得多少
 * @return string
 */
function get_site_url($http = true)
{
    $string = $_SERVER['REQUEST_URI'];
    $string = empty($string) ? $_SERVER['QUERY_STRING'] : $string;
    $url    = $_SERVER['HTTP_HOST'] . '/' . $string;
    return ($http ? 'http://' : '') . str_replace('//', '/', $url);
}

/**
 * 获取来路
 */
function get_referer()
{
    return $_SERVER['HTTP_REFERER'];
}

function __parse($file)
{
    if (!is_file($file))
        return '';
    $content = php_strip_whitespace($file);
    $content = substr(trim($content), 5);
    if ('?>' == substr($content, -2))
        $content = substr($content, 0, -2);
    return $content;
}
function read_dir($dir, $show_info = false, $file_name = false, $prefix = false, $show_size = false, $show_stat = false)
{
    $results     = array();
    $folder_list = array(
        $dir
    );
    $temp        = array();
    while (true) {
        if (!$folder_list)
            break;
        $_this_dir = array_pop($folder_list);
        $hd        = opendir($_this_dir);
        while (false != $_this_file = readdir($hd)) {
            if ($_this_file == '.' || $_this_file == '..')
                continue;
            $path = $_this_dir . '/' . $_this_file;
            $file_name ? $temp['name'] = $_this_file : '';
            $temp['full'] = $path;
            if (is_dir($path)) {
                $folder_list[] = $path;
                $type          = 'd';
            } else {
                if ($prefix) {
                    $pathinfo       = pathinfo($_this_file);
                    $temp['prefix'] = $pathinfo['extension'];
                }
                $show_stat ? $temp['stat'] = stat($path) : '';
                $show_size ? $temp['size'] = filesize($path) : '';
                $type = 'f';
            }
            $show_info ? $temp['type'] = $type : '';
            $results[] = $temp;
        }
        closedir($hd);
    }
    unset($folder_list, $path, $dir, $temp);
    return $results;
}
function __auto_load($epath)
{
    //$epath = str_replace('\\', '/', realpath('./')).'/';
    //$epath = empty($epath)?SITE_ROOT:$epath;
    $r            = $epath . 'Runtime/';
    $end_fun_file = $r . '~fun.php';
    if (!is_file($end_fun_file)) {
        $data     = '';
        $dir      = $epath . 'Common';
        $path     = read_dir($dir, true);
        $filelist = array();
        foreach ($path as $file) {
            $file = $file['full'];
            $ext  = trim(substr($file, -8, 8));
            if ($ext == '.fun.php') {
                $filelist[] = $file;
            }
        }
        if ($filelist) {
            foreach ($filelist as $e)
                $data .= __parse($e);
            mkdirs_a($r);
            if (!file_put_contents($end_fun_file, '<?php ' . $data))
                exit('please check the web root path is can write!');
        }
        unset($data, $filelist);
    }
    !function_exists('require_cache') ? require_once $end_fun_file : require_cache($end_fun_file);
}

/**
 * 把int转换成kb或mb等
 * @param int $bytes
 * @return string
 */
function format_bytes($bytes, $showBt = true)
{
    $display = array(
        'Byte',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB',
        'EB',
        'ZB',
        'YB'
    );
    $level   = 0;
    while ($bytes > 1024) {
        $bytes /= 1024;
        $level++;
    }
    return round($bytes, 2) . ' ' . ($showBt ? $display[$level] : '');
}

function __debug($echo = true, $exit = true, $common = true, $file = true, $sql = true)
{
    $trace       = array();
    $temp_string = '<style type="text/css">
	#debug_info_pannel{ clear:both; display:block;}
	#debug_info_pannel fieldset{ clear:both; display:block; border:1px  solid #EAEAEA; height:auto; padding:10px;margin:10px;}
	#debug_info_pannel fieldset legend{ font-weight:bold;}
</style><div id="debug_info_pannel">';
    if ($common) {
        $temp_string .= '<fieldset><legend>系统信息</legend>';
        $trace[] = '执行时间:' . _showTime();
        $trace[] = '当前页面:' . $_SERVER['REQUEST_URI'];
        $trace[] = ('请求方法:' . $_SERVER['REQUEST_METHOD']);
        $trace[] = ('通信协议:' . $_SERVER['SERVER_PROTOCOL']);
        $trace[] = ('请求时间:' . date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
        $trace[] = ('用户代理:' . $_SERVER['HTTP_USER_AGENT']);
        $trace[] = ('会话ID:' . session_id());
        if (MEMORY_LIMIT_ON) {
            $trace[] = '内存占用:' . format_bytes(memory_get_usage(true));
        }
        $temp_string .= join('<br/>', $trace);
        $temp_string .= "</fieldset>";
    }
    if ($file) {
        $files = get_included_files();
        if ($files) {
            $temp_string .= '<fieldset><legend>文件加载清单</legend>';
            foreach ($files as $key => $value) {
                $temp_string .= '' . ($key + 1) . '、' . $value . "<br />";
            }
            $temp_string .= "</fieldset>";
        }
    }
    if ($sql) {
        $temp_string .= '<fieldset><legend>SQL信息</legend>';
        $dsql = D()->log();
        if ($dsql) {
            $arg = '';
            foreach ($dsql as $k => $sql) {
                $temp_string .= $arg . ($k + 1) . '、' . $sql;
                $arg = '<br />';
            }
        }
        $temp_string .= "</fieldset>";
        $temp_string .= '</div>';
    }
    if ($echo) {
        echo '<pre>';
        print_r($temp_string);
        echo '</pre>';
    }
    return $exit ? exit('') : $temp_string;
}

function _showTime()
{
    G('viewStartTime');
    $showTime = 'Process: ' . G('beginTime', 'viewEndTime') . 's ';
    if (C('SHOW_ADV_TIME')) {
        $showTime .= '( Load:' . G('beginTime', 'loadTime') . 's Init:' . G('loadTime', 'initTime') . 's Exec:' . G('initTime', 'viewStartTime') . 's Template:' . G('viewStartTime', 'viewEndTime') . 's )';
    }
    if (C('SHOW_DB_TIMES') && class_exists('Db', false)) {
        $showTime .= ' | DB :' . N('db_query') . ' queries ' . N('db_write') . ' writes ';
    }
    if (C('SHOW_CACHE_TIMES') && class_exists('Cache', false)) {
        $showTime .= ' | Cache :' . N('cache_read') . ' gets ' . N('cache_write') . ' writes ';
    }
    if (MEMORY_LIMIT_ON && C('SHOW_USE_MEM')) {
        $startMem = array_sum(explode(' ', $GLOBALS['_startUseMems']));
        $endMem   = array_sum(explode(' ', memory_get_usage()));
        $showTime .= ' | UseMem:' . number_format(($endMem - $startMem) / 1024) . ' kb';
    }
    return $showTime;
}

function check_tpl_exist($file){
	$file_end = C('TPL_MAIN_PATH').'/'.C('DEFAULT_THEME').'/'.$file;
	return is_file($file_end);
}

/**
 * 短连接生成规则
 * @param int $adid 广告ID
 * @param int $length 长度
 * @Author feng.yin@baifendian.com | 薛红贺
 */
function get_short_link_url($adid, $length = 7)
{
    $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-=";
    //将分段的位与0x3fffffff做位与，0xffffffff表示二进制数的32个1，即八位十六禁止
    $hex     = $adid ^ 0x2143a5cb & 0xffffffff;
    $code    = "";
    //生成6位短连接
    for ($j = 0; $j < $length; $j++) {
        //将得到的值与0x0000003d,3d为61，即charset的坐标最大值
        $code .= $charset[$hex & 0x0000003f];
        //循环完以后将hex右移5位
        $hex = $hex >> 5;
        if (0 == $hex && strlen($code) > 5)
            break;
    }
    return $code;
}
function get_advertise_link($adid)
{
    if (empty($adid))
        return '';
    return C('amp_short_link') . get_short_link_url($adid);
}
__auto_load(SITE_ROOT);

/**
 * 生成radio
 * @return string
 * @author feng.yin
 * @date
 * @version
 */
function make_radio($arr, $name, $number, $is_disabled = 0)
{
    if (!is_array($arr))
        return $arr;
    $str = '';
    foreach ($arr as $k => $v) {
        $checked = ($k == $number) ? 'checked' : '';
        if ($is_disabled == '1') {
            $str .= "<input type='radio' id='$name$k' name='$name' disabled value='$k' $checked>$v&nbsp;";
        } else {
            $str .= "<input type='radio' id='$name$k' name='$name' value='$k' $checked>$v&nbsp;";
        }
    }
    return substr($str, 0, -6);
}

function make_radio_ex($arr, $name, $number, $is_disabled = 0) {
    if (!is_array($arr))
        return $arr;
    $str = '';
    foreach ($arr as $k => $v) {
        $checked = ($k == $number) ? 'checked' : '';
        if ($is_disabled == '1') {
            $str .= "<span id='s_$name$k'><input type='radio' id='$name$k' name='$name' disabled value='$k' $checked>$v&nbsp;</span>";
        } else {
            $str .= "<span id='s_$name$k'><input type='radio' id='$name$k' name='$name' value='$k' $checked>$v&nbsp;</span>";
        }
    }
    return $str;
}

function db_tag_new() {
    return D('dbExtend');
}

/**
 * 多线程请求执行
 * @param unknown_type $urls
 * @return multitype:|string
 * @author feng.yin@baifendian.com
 * @date 2012-9-4下午3:34:26
 */
function _ddp_complicating_get_data_by_curl($urls)
{
    $urls  = (array) $urls;
    $start = microtime(true);
    if (empty($urls))
        return array();
    $curl_handle = curl_multi_init();
    $connect     = array();
    foreach ($urls as $key => $url) {
        $connect[$key] = curl_init($url);
        curl_setopt($connect[$key], CURLOPT_RETURNTRANSFER, 1);
        curl_multi_add_handle($curl_handle, $connect[$key]);
    }
    do {
        $status = curl_multi_exec($curl_handle, $active);
        $info   = curl_multi_info_read($curl_handle);
    } while ($status === CURLM_CALL_MULTI_PERFORM || $active);
    
    foreach ($urls as $key => $url) {
        //$res[$key] = curl_multi_getcontent($connect[$key]);
        $res = curl_multi_getcontent($connect[$key]);
        curl_close($connect[$key]);
    }
    $end = microtime(true);
    $ee  = $end - $start;
    if (_ddp_is_debug_model()) {
        __e('多线程请求时间：' . $ee);
        __e('多线程请求URL：' . join('<br />', $urls));
    }
    $_ENV['__call_ddp_time__'][] = $ee;
    return $res;
}

$_GET = html_encode($_GET);

function _ddp_is_debug_model()
{
    return !empty($_GET['debug']) ? true : false;
}

/**
 * 根据广告位标签$type(1)/广告内容标签$type(2)获取相关标签
 * @return return_type
 * @author feng.yin
 * @date 
 * @version 
 */
function getTagsbytype($type)
{
    $type = to_int($type);
    $tags = D('tags')->where("`type`=$type and `status`=1")->select();
    return $tags;
}

/**
 * 
 * 发送短信给指定的人 包括短信和邮箱
 * @Author feng.yin@baifendian.com
 * @param string $flag adplan|ad
 * @param string $id   adplan|ad 对应的ID
 * @throws Exception
 * @Author feng.yin@baifendian.com
 */
function sent_admin_message($flag, $id){
	return false;
   
}
/**
 * 发短信
 * @Author feng.yin@baifendian.com
 */
function send_sms_msg($mobile_no, $body)
{
    $call_mms_url  = C('mmc_sent_msg_config.call_mms_url');
    $call_mms_type = C('mmc_sent_msg_config.call_mms_type');
    $mobile_no     = !is_array($mobile_no) ? explode(',', $mobile_no) : (array) $mobile_no;
    $post_data     = array(
        'phone' => join(',', $mobile_no),
        'text' => trim($body),
        'type' => $call_mms_type,
        'extends' => ''
    );
    $callback      = send_sms_post_data($post_data, $call_mms_url);
    $status        = $callback['msg'];
    if ($status != 'success') {
        $error = array(
            '__status_msg__' => __r('发送短信不成功!'),
            '__call_back_msg__' => $callback,
            '__post_data__' => $post_data,
            '__call_parames__' => func_get_args()
        );
        __sent_mail($error);
    }
    return $status;
}
/**
 * 发短信底层数据
 * 
 * $post_data = array(
 'phone'=>'13126559959,18801358429,15102454545',
 'text'=>'这是很长度 发信内容，这是很长度 发信内容，这是很长度 发信内容，
 这是很长度 发信内容，这是很长度 发信内容，这是很长度 发信内容，这是很长度 发信内容，
 这是很长度 发信内容，这是很长度 发信内容，这是很长度 发信内容，这是很长度 发信内容，这是很长度 发信内容，这是很长度 发信内容，这是很长度 发信内容，',
 'type'=>'10',
 'extends'=>'',
 );
 $call_msg_url = '10.0.2.130/smsproxy.php';
 $msg = post_data($post_data,$call_msg_url);
 var_dump($msg);
 * @param unknown $post_data
 * @param unknown $call_msg_url
 * @return multitype:|Ambigous <string, mixed>
 * Author feng.yin@baifendian.com
 * Date 2013-4-16
 */
function send_sms_post_data($post_data, $call_msg_url)
{
    if (empty($post_data) || !is_array($post_data) || empty($call_msg_url)) {
        return array(
            '__call_fun_arg__' => func_get_args(),
            '__call_error_msg__' => '参数传递有异常',
            '__call_method__' => __FUNCTION__
        );
    }
    $append_data = http_build_query($post_data);
    $ch          = curl_init();
    $u           = substr($call_msg_url, 0, 7) != 'http://' ? 'http://' . $call_msg_url : $call_msg_url;
    curl_setopt($ch, CURLOPT_URL, $u);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    //curl_setopt ( $ch,  CURLOPT_NOSIGNAL, true);
    //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200); //毫秒
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //设置是将获取的数据返回而不显示
    curl_setopt($ch, CURLOPT_POSTFIELDS, $append_data);
    $res = trim(curl_exec($ch));
    if (!empty($res)) {
        create_file(var_export(array(
            'msg' => $res,
            'time' => date("YmdHis"),
            'file' => get_included_files(),
            'request' => $_REQUEST
        ), true) . "\r\n", SITE_ROOT . 'log/mms_curl_call_error.log', 'ab+');
        //var_dump($msg,$callback);
    }
    curl_close($ch);
    if ($res) {
        $res = json_decode($res, true);
    }
    return (array) $res;
}
/**
 * 通知用户发送短信 异步执行
 * @Author feng.yin@baifendian.com
 */
function send_mobile_msg($mobile, $id, $flag)
{
    $post_data['reciver'] = $mobile;
    $post_data['id']      = $id;
    $post_data['flag']    = $flag;
    $link                 = '/index.php/syn/send_notice_mobile_info/';
    do_syn($link, $post_data);
}

/**
 * @param unknown_type $http		是否加http前缀
 * @param unknown_type $adv_type	判断是否是铃音 7 是铃音
 * @return Ambigous <string, unknown>
 */
function _get_material_url($http = true, $adv_type = 0)
{
    if ($adv_type == 7) {
        $append = C('RING_CREATEFTPSERVER_HTTP_URL_APPEND');
        $url    = C('RING_CREATEFTPSERVER_HTTP_URL') . (empty($append) ? '' : '/' . $append . '/');
    } else {
        $append = C('CREATEFTPSERVER_HTTP_URL_APPEND');
        $url    = C('CREATEFTPSERVER_HTTP_URL') . (empty($append) ? '' : '/' . $append . '/');
    }
    $url = substr($url, -1, 1) != '/' ? $url . '/' : $url;
    return $http ? 'http://' . $url : $url;
}
function toOneA()
{
    return sprintf("%c", 0x01);
}
/**
 * 返回首字母
 * @param unknown_type $s0
 * @return string|NULL
 * @author muyunchao
 */
function getfirstchar($s0)
{
    $fchar = ord($s0{0});
    if ($fchar >= ord("A") and $fchar <= ord("z"))
        return strtoupper($s0{0});
    @$s1 = iconv("UTF-8", "gb2312", $s0);
    @$s2 = iconv("gb2312", "UTF-8", $s1);
    if ($s2 == $s0) {
        $s = $s1;
    } else {
        $s = $s0;
    }
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 and $asc <= -20284)
        return "A";
    if ($asc >= -20283 and $asc <= -19776)
        return "B";
    if ($asc >= -19775 and $asc <= -19219)
        return "C";
    if ($asc >= -19218 and $asc <= -18711)
        return "D";
    if ($asc >= -18710 and $asc <= -18527)
        return "E";
    if ($asc >= -18526 and $asc <= -18240)
        return "F";
    if ($asc >= -18239 and $asc <= -17923)
        return "G";
    if ($asc >= -17922 and $asc <= -17418)
        return "I";
    if ($asc >= -17417 and $asc <= -16475)
        return "J";
    if ($asc >= -16474 and $asc <= -16213)
        return "K";
    if ($asc >= -16212 and $asc <= -15641)
        return "L";
    if ($asc >= -15640 and $asc <= -15166)
        return "M";
    if ($asc >= -15165 and $asc <= -14923)
        return "N";
    if ($asc >= -14922 and $asc <= -14915)
        return "O";
    if ($asc >= -14914 and $asc <= -14631)
        return "P";
    if ($asc >= -14630 and $asc <= -14150)
        return "Q";
    if ($asc >= -14149 and $asc <= -14091)
        return "R";
    if ($asc >= -14090 and $asc <= -13319)
        return "S";
    if ($asc >= -13318 and $asc <= -12839)
        return "T";
    if ($asc >= -12838 and $asc <= -12557)
        return "W";
    if ($asc >= -12556 and $asc <= -11848)
        return "X";
    if ($asc >= -11847 and $asc <= -11056)
        return "Y";
    if ($asc >= -11055 and $asc <= -10247)
        return "Z";
    return null;
}
/**
 * 返回汉字的拼音
 * @param unknown_type $zh
 * @return Ambigous <string, NULL>
 * @author muyunchao
 */
function pinyin($zh)
{
    $ret = "";
    @$s1 = iconv("UTF-8", "gb2312", $zh);
    @$s2 = iconv("gb2312", "UTF-8", $s1);
    if ($s2 == $zh) {
        $zh = $s1;
    }
    for ($i = 0; $i < strlen($zh); $i++) {
        $s1 = substr($zh, $i, 1);
        $p  = ord($s1);
        if ($p > 160) {
            $s2 = substr($zh, $i++, 2);
            $ret .= getfirstchar($s2);
        } else {
            $ret .= $s1;
        }
    }
    return $ret;
}
/**
 * 返回分表对应的表
 * @param $k:对应相应分表的id
 * @param $m：对应分表的散列值
 * @return hash <string, NULL>
 * @author liuqing
 */
function get_table_hash($k, $m = 10){
    //把字符串K转换为1～m之间的一个值作为对应记录的散列地址
    $l    =    strlen($k);
    $b    =    bin2hex($k);
    $h    =    0;
    for($i=0;$i<$l;$i++){
        //采用一种方法计算K所对应的整数
        $h    +=    substr($b,$i*2,2);
    }
    $hash    =    ($h*1)%$m + 1;
    return    $hash;
}

/**
  * Curl函数
  * @param $urls string 请求地址
  * @param $model string 模式
  * @param $params string 参数
  * @return data
*/
function _call_server($urls, $params = '', $debug=false, $https = 0) {
   if($debug){
    	echo '请求java实时发彩信服务端的URL地址是<br />';
    	dump($urls);
    	echo '请求java实时发彩信服务端的参数是<br />';
    	dump($params);
   }
   $s_timeout = 3;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $urls);
   curl_setopt($ch, CURLOPT_HEADER, false);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_TIMEOUT, $s_timeout);
   if ($https) {
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
   }
   if (!empty($params)) {
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
   }
   $data = curl_exec($ch);
   curl_close($ch);
   if($debug){
    	echo '请求服务器返回的参数<br />';
    	dump($data);
   }
   return $data;
}
function create_order_sn(){
	list($usec, $time) = explode(' ', microtime());
	return  date('YmdHis', $time) . ($usec * 100000000) . mt_rand(1000, 9999);
}
function get_order_sn(){
	return create_order_sn();
}
function __rand_string($length,$type = 'ALL') {
	$hash = '';
	switch ($type){
		case 'max':
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			break;
		case 'num':
			$chars = '0123456789';
			break;
		case 'en':
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
		default:
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	}
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++){
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}
function format_money($price,$ext = '元'){
	return  number_format($price, 2, '.', ',').$ext;
}
function format_money_two($price){
	return  number_format($price, 2, '.', ',');
}
function uploadBase64Img($upfile) {
	if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $upfile, $result)){
		$type = $result[2];
		$datepath = date('Ym');
		$filepath = C('COMPANY_UPLOAD.FILE_PATH');
		is_dir($filepath) or mkdir($filepath);
		$filepath = $filepath . $datepath;
		is_dir($filepath) or mkdir($filepath);
		$filename = date('YmdHis') . mt_rand(1000, 9999) .'.'. $type;
		$imgfile = $filepath . '/' . $filename;
		if (file_put_contents($imgfile, base64_decode(str_replace($result[1], '', $upfile)))){
			$_file = $imgfile;
			$flag = upload_images_by_ftp($_file,$_file);
			if ( $flag ) {
				//@unlink($savedata['stuff_url']);
			}else{
				exit("FTP_IS_FAIL");
			}
			return C('COMPANY_UPLOAD.IMG_PATH') . $datepath .'/'. $filename;
		} else {
        	exit('ERROR_UPFILE');
    	}
	} else {
		return $upfile;
	}
}
function user_uploadpic($upfile) {
    if (empty($upfile['size']) || $upfile['error']) {
        return 'ERROR_UPFILE';
    }
    
    $filetype = C('COMPANY_UPLOAD.FILE_TYPE');
    $pathinfo = pathinfo($upfile['name']);
    if (!in_array(strtolower($pathinfo['extension']), $filetype)) {
        return  'ERROR_UPEXT';
    }
    if (in_array(strtolower($pathinfo['extension']), array('doc', 'docx'))) {
    	$filesize = C('COMPANY_UPLOAD.FILE_CASE_SIZE');
    } else {
    	$filesize = C('COMPANY_UPLOAD.FILE_SIZE');
    }
    if ($upfile['size'] > $filesize) {
        return 'ERROR_UPSIZE';
    }
    
    $datepath = date('Ym');
    $filepath = C('COMPANY_UPLOAD.FILE_PATH');
    is_dir($filepath) or mkdir($filepath);
    $filepath = $filepath . $datepath;
    is_dir($filepath) or mkdir($filepath);
    $filename = date('YmdHis') . mt_rand(1000, 9999) .'.'. $pathinfo['extension'];
    
    $tmpfile = $upfile['tmp_name'];
    $imgfile = $filepath . '/' . $filename;
    
    if (move_uploaded_file($tmpfile, $imgfile)) {
        return C('COMPANY_UPLOAD.IMG_PATH') . $datepath .'/'. $filename;
    } else {
        return 'ERROR_UPFILE';
    }
}
function _syn_send_email($mail,$title,$content){
	$post_data['title'] = $title;
	$post_data['mail'] = $mail;
	$post_data['content'] = $content;
	$link = '/syn/system_send_notice_mail/';
	do_syn($link,$post_data);
}
function start_send_notice_email($model,$id,$reason_content){
	$id = to_int($id);
	if(empty($model) || empty($id))return false;
	$config = C('__send_mail_config__');
	switch ($model){
		//APP审核不通过
		case 'app_unpass':
			$member_data_sql = "SELECT
			m.`email`,m.`mobile`,m.`username`,i.`app_name`
			FROM `atd_app_info` AS i
			LEFT JOIN `atd_members` AS m
			ON i.`uid` = m.`uid`
			WHERE i.`app_id` = '$id' limit 0,1
			";
			$set = $config['app'];
			if(!$set['is_send'])return false;
			$find_data = D()->query($member_data_sql,true);
			if(empty($find_data))return false;
			$title = str_replace(array('{$app_name}'), array($find_data['app_name']), $set['title']);
			//'content'=> '用户{$user_name}您好:<br />您的应用{$app_name}因{$reason}原因审核不通过,特此告知，若有疑问请联系管理员',
			$content = str_replace(
					array('{$user_name}','{$app_name}','{$reason}'), 
					array($find_data['username'],$find_data['app_name'],$reason_content), $set['content']);
			_syn_send_email($find_data['email'],$title,$content);
			break;
		//广告计划审核不通过
		case 'adplan_unpass':
				$sql = "
					SELECT p.`name`,m.`username`,m.`uid`,m.`email` FROM `atd_advertise_plan` AS p 
					LEFT JOIN (
					`atd_members` AS m 
					)ON (
					  m.`uid` = p.`advertiser`
					)WHERE  p.`adplan_id` = '$id' LIMIT 0,1 
				";
				$set = $config['adplan_unpass'];
				if(!$set['is_send'])return false;
				$find_data = D()->query($sql,true);
				if(empty($find_data))return $find_data;
				$title = str_replace(array('{$name}'), array($find_data['name']), $set['title']);
				/**
			  'title'=>'【审核通知】广告计划{$name}审核不通过',
				'content'=> '用户{$user_name}您好:<br />您的广告计划{$name}因{$reason}原因审核不通过,特此告知，若有疑问请联系管理员。 <br />'
						 */
						$content = str_replace(
						array('{$user_name}','{$name}','{$reason}'),
								array($find_data['username'],$find_data['name'],$reason_content), $set['content']);
			_syn_send_email($find_data['email'],$title,$content);
		break;
		//广告未通过
		case 'ad_unpass':
			$sql = "
				SELECT a.`advertise_name` AS ad_name,m.`uid`,m.`username`,m.`email` FROM `atd_advertising` AS a
				LEFT JOIN (
				   `atd_advertise_group_position_relation` AS r,
				   `atd_members`    AS m
				)ON (
				  a.`advertise_id` = r.`ad_id`
				  AND r.`advertiser_id` = m.`uid`
				) WHERE a.`advertise_id` = '$id' LIMIT 0,1;
			";
				$set = $config['ad_unpass'];
				if(!$set['is_send'])return false;
				$find_data = D()->query($sql,true);
				if(empty($find_data))return false;
				$title = str_replace(array('{$name}'), array($find_data['ad_name']), $set['title']);
				/**
				'is_send'=>true,
				'title'=>'【审核通知】您的广告{$name}审核不通过',
				'content'=> '用户{$user_name}您好:<br />您的广告{$name}因{$reason}原因审核不通过,特此告知，若有疑问请联系管理员。 <br />'
				.date("Y-m-d",time()),
				*/
			$content = str_replace(
			array('{$user_name}','{$name}','{$reason}'),
			array($find_data['username'],$find_data['ad_name'],$reason_content), $set['content']);
			_syn_send_email($find_data['email'],$title,$content);
			break;
		//用户密码修改
		case 'user_pass_changed':
			$member_data_sql = "SELECT m.`uid`,m.`username`,m.`email`
			FROM 
				`atd_members` AS m WHERE m.`uid` = '$id' LIMIT 0,1 ";
			$set = $config['user_pass_changed'];
			if(!$set['is_send'])return false;
			$find_data = D()->query($member_data_sql,true);
			if(empty($find_data))return false;
			$title = $set['title'];
			$title = str_replace(array('{$user_name}'), array($find_data['username']), $title);
			$content = str_replace(
			array('{$user_name}','{$reason}'),
					array($find_data['username'],$reason_content),
					$set['content']
			);
			_syn_send_email($find_data['email'],$title,$content);
		break;
		//用户账号新建
		case 'user_create_send_password':
			$member_data_sql = "SELECT m.`uid`,m.`username`,m.`email`
			FROM
			`atd_members` AS m WHERE m.`uid` = '$id' LIMIT 0,1 ";
			$set = $config['user_create_send_password'];
			if(!$set['is_send'])return false;
			$find_data = D()->query($member_data_sql,true);
			if(empty($find_data))return false;
			$title = $set['title'];
			$title = str_replace(array('{$user_name}'), array($find_data['username']), $title);
			$content = str_replace(
					array('{$user_name}','{$reason}'),
					array($find_data['username'],$reason_content),
					$set['content']
			);
			_syn_send_email($find_data['username'],$title,$content);
			break;
	//用户审核不通过
		case 'verify_user':
			$member_data_sql = "SELECT
				m.`email`,m.`mobile`,m.`username`
				FROM `atd_members` AS m
				WHERE m.`uid` = '$id' limit 0,1
			";
			$set = $config['verify_user'];
			if(!$set['is_send'])return false;
			$find_data = D()->query($member_data_sql,true);
			$title = $set['title'];
			$title = str_replace(array('{$user_name}'), array($find_data['username']), $title);
			$content = str_replace(
					array('{$user_name}','{$reason}'),
					array($find_data['username'],$reason_content),
					$set['content']
			);
			_syn_send_email($find_data['email'],$title,$content);
			break;
	}
}
/**
 * FTP上传文件
 * @param string $remote_file
 * @param string $local_file
 */
function upload_images_by_ftp($remote_file,$local_file) {
	if( !C('open_ftp') ){
		return true;
	}
	if(empty($remote_file) || empty($local_file)) return false;
	import("@.ORG.Ftp");
	$ftp = new Ftp(C('__UPLOAD_FILE_FTP_CONFIG__'));
	//$local_file = ROOT_PATH.'upload/test/468_60.jpg';
	$remote_file = str_replace(ROOT_PATH, '', $local_file);
	$remote_file = substr($remote_file,0,1)!='/'?'/'.$remote_file:$remote_file;
	$callback = $ftp->upload_one($local_file, $remote_file);
	return $callback;
}
/**
 * 从FTP服务器上删除文件
 * @param unknown $remote_file
 * @return boolean
 */
function delete_images_by_ftp($remote_file){
	if(empty($remote_file))return false;
	import("@.ORG.Ftp");
	$ftp = new Ftp(C('__UPLOAD_FILE_FTP_CONFIG__'));
	$callback = $ftp->delete($remote_file);
	return $callback;
}
/**
 * 解压gzip文件
 */
if ( !function_exists('gzdecode') ) {
	function gzdecode ($data) {
		$flags = ord(substr($data, 3, 1));
		$headerlen = 10;
		$extralen = 0;
		$filenamelen = 0;
		if ($flags & 4) {
			$extralen = unpack('v' ,substr($data, 10, 2));
			$extralen = $extralen[1];
			$headerlen += 2 + $extralen;
		}
		if ($flags & 8) // Filename
			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 16) // Comment
			$headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 2) // CRC at end of file
			$headerlen += 2;
		$unpacked = @gzinflate(substr($data, $headerlen));
		if ($unpacked === FALSE)
			$unpacked = $data;
		return $unpacked;
	}
}
/**
 * 是否关闭报表DEBUG模式 当为true时关闭
 * @return boolean
 */
function is_close_report_debug_model(){
	$c = C('CLOSE_REPORT_DEBUG_MODEL');
	return $c?true:false;
}
/**
 * 是否关闭APOLLO DEBUG模式 当为true时关闭  即不显示debug模式
 * @return boolean
 */
function is_close_apollo_debug_model(){
	return C('CLOSE_APOLLO_DEBUG_MODEL');
}
/**
 * 是否关闭AMC DEBUG模式 当为true时关闭  即不显示debug模式
 * @return boolean
 */
function is_close_amc_debug_model(){
	return C('CLOSE_AMC_DEBUG_MODEL');
}
?>