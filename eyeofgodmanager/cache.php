<?php 
/**
 * 删除指定文件夹 默认删除自身
 */
function delete_folder($dir, $delete_self = true) {
    $folder_list = array($dir);
    $delete_self ? $temp[] = $dir : $temp = array();
    while (true) {
        if (!$folder_list)break;
        $_this_dir = array_pop($folder_list);
        $hd = opendir($_this_dir);
        while (false != $_this_file = readdir($hd)) {
            if ($_this_file == '.' || $_this_file == '..'

                )continue;
            $path = $_this_dir . '/' . $_this_file;
            if (is_dir($path)) {
                $folder_list[] = $path;
                $temp[] = $path;
            }
            @unlink($path);
        }
        closedir($hd);
    }
    foreach (array_reverse($temp) as $item )@rmdir($item);
    unset($temp, $folder_list, $dir);
    return true;
}
$dir = str_replace('\\', '/',dirname(__FILE__)).'/Runtime/';
delete_folder($dir);

$dir = str_replace('\\', '/',dirname(__FILE__)).'/log/amc/'.date('Ymd',time());
$remote_ip = $_SERVER["REMOTE_ADDR"];
if($remote_ip == '127.0.0.1'){
	delete_folder($dir);
}
echo 'ok';
?>