<?php
/*
 * author : liutao
 * function : transport data from DDP
 */

class ddp {

    var $curl;
    var $source;

    function __construct() {
        $this->curl = curl_init();
        $this->source = C('DDP_SOURCE');
    }

    function get_content_from_source($ch, $url) {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        return $data;
    }

    function getAllWebsite() {
        $data = $this->get_content_from_source($this->curl, $this->source . "websiteids/");
        $pattern = '/[\[|\]]/';
        $website = array();
        $replacedata = preg_replace($pattern, "", $data);
        $site = explode(',', $replacedata);
        foreach ($site as $val) {
            $val = preg_replace("/[\s|\'|\"]/", "", $val);
            $website[] = $val;
        }
        return $website;
    }

    function getWebsites($method){
         $data = $this->get_content_from_source($this->curl, $this->source . $method."/");
         $site = json_decode($data);
         return $site;
    }

    function get_site_info($method,$websiteid, $from='', $to='') {
        $url = '';
        if ($websiteid != '' && $from != '' && $to != '') {
           $url = $this->source .$method.'/'. $websiteid . '/?f=' . $from . '&t=' . $to;
        } else if($websiteid!='') {
            $url = $this->source . $websiteid;
        } else {
            return '';
        }
            curl_setopt($this->curl, CURLOPT_URL, $url);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($this->curl);
            if(!empty($data)) {
                return $data;
            } else {
                return '';
            }
        }
}

?>
