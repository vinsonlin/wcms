<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat extends CI_Controller {

	private $_token = "weixin";

	/**
	 * 微信接口调试
	 */
	public function index()
	{
		//$this->load->view('welcome_message');
		$this->valid();
		echo  "hello world!";
	}
	/**
	 * 微信验证
	 * @return [type]
	 */
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
	/**
	 * 微信验证
	 * @return [type]
	 */
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!$this->_token) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = $this->_token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}
?>