<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wechat extends CI_Controller {

	private $_token = "374639f832f5f6d1881fe79bee812a01c32e881c";

	/**
	 * 微信接口调试
	 */
	public function index()
	{
		//$this->load->view('welcome_message');
		$this->valid();
		//echo  "hello world!";
	}

	private function msgTpl($type = 'text'){
		$tpl = "";
		switch($type){
			case 'text':
				$tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";
			break;
			case 'image':
				$tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[image]]></MsgType>
					<Image>
					<MediaId><![CDATA[%s]]></MediaId>
					</Image>
					</xml>";
				break;
			case 'video':
				$tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[video]]></MsgType>
					<Video>
					<MediaId><![CDATA[%s]]></MediaId>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					</Video> 
					</xml>";
			break;
			case 'voice':
				$tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[voice]]></MsgType>
					<Voice>
					<MediaId><![CDATA[%s]]></MediaId>
					</Voice>
					</xml>";
			break;
			case 'music':
				$tpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[music]]></MsgType>
					<Music>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					<MusicUrl><![CDATA[%s]]></MusicUrl>
					<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
					<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
					</Music>
					</xml>";
			break;
			case 'tuwen':
				$tpl ="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>2</ArticleCount>
					<Articles>
					<item>
					<Title><![CDATA[title1]]></Title> 
					<Description><![CDATA[description1]]></Description>
					<PicUrl><![CDATA[picurl]]></PicUrl>
					<Url><![CDATA[url]]></Url>
					</item>
					<item>
					<Title><![CDATA[title]]></Title>
					<Description><![CDATA[description]]></Description>
					<PicUrl><![CDATA[picurl]]></PicUrl>
					<Url><![CDATA[url]]></Url>
					</item>
					</Articles>
					</xml>";
			break;
		}
		return $tpl;
	}
	/**
	 * 消息回复
	 */
	public function responseMsg()
    	{	
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      		//extract post data
		if (!empty($postStr)){
                	/* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                  	 the best way is to check the validity of xml by yourself */
                	libxml_disable_entity_loader(true);
              		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                	$fromUsername = $postObj->FromUserName;
                	$toUsername = $postObj->ToUserName;
			$msgType = $postObj->MsgType;
                	$keyword = trim($postObj->Content);
                	$time = time();
			$resultStr = "";
			switch($msgType){
				case 'image':
					$tpl = $this->msgTpl('image');
					$resultStr = sprintf($tpl, $fromUsername,$toUsername, $time, $postObj->MediaId);
				break;
				case 'text':
					$tpl = $this->msgTpl('text');
					$resultStr = sprintf($tpl, $fromUsername,$toUsername,$time,$keyword);
				break;
			}
			echo $resultStr;
/*                	$textTpl = "<xml>
				    <ToUserName><![CDATA[%s]]></ToUserName>
		        	    <FromUserName><![CDATA[%s]]></FromUserName>
				    <CreateTime>%s</CreateTime>
			            <MsgType><![CDATA[%s]]></MsgType>
				    <Content><![CDATA[%s]]></Content>
				    <FuncFlag>0</FuncFlag>
				    </xml>";             
			if(!empty( $keyword ))
                	{
              			//$msgType = "text";
                		$contentStr = $keyword;
                		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                		echo $resultStr;
               		 }else{
                		echo "Input something...";
               		 }
*/
        	}else {
        		echo "";
        		exit;
        	}
    	}

	/**
	 * 微信验证
	 * @return [type]
	 */
	public function valid()
    	{	
        	//$echoStr = $_GET["echostr"];
        	//valid signature , option
        	if($this->checkSignature()){
        		//echo $echoStr;
			$this->responseMsg();
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
