<?php
session_start();

$ROOTDIR=dirname(__FILE__)."/../../code_chi/";
$CURDIR=dirname(__FILE__);
define("TOKEN", "ruibaochi");
require_once($CURDIR."/wx_sample.php");
require_once($ROOTDIR."/queue/queue.php");
require_once($ROOTDIR."/file/file.php");
header("Content-Type: text/html; charset=gb2312");

define ('bizname', "self_test");

$subq = "";
if (init_q($subq, $sub_queue_file, "p") === false)
{
	wx_log(__FILE__."_".__LINE__.":"."ERR init_ftok $sub_queue_file !");
	exit;
}

function intoq(&$content, &$toUsername, &$fromUsername)
{
	//get post data, May be due to the different environments
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

	global $subq;
	//extract post data
	if (!empty($postStr)){

		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$toUsername = $postObj->ToUserName;
		$fromUsername = $postObj->FromUserName;
		$time = $postObj->CreateTime;
		$msgType = $postObj->MsgType;
		if (strcmp ($msgType, "image") === 0)
			$content = $postObj->PicUrl;
		else
			$content = $postObj->Content;
		if (strcmp ($msgType, "event") === 0)
			return $msgType;
		$bizname = bizname;
		$msg = $bizname."&&".$fromUsername."&&".$time."&&".$content;
		msg_send($subq, 1, $msg);
		return $msgType;
	}
}

function do_echo($from, $to)
{
	$bizname = bizname;
	$msg = get_content($bizname, "subscribe");
	$pre = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>";
	$premsg = sprintf($pre, $to, $from, time());
	$msg = $premsg.$msg;
	wx_log("echo:".$msg."\n");
	echo $msg;
}

function do_rsp($c, $from, $to)
{
	do_echo($from, $to);
}

$url = $_SERVER['REQUEST_URI'];
$wechatObj = new wechatCallbackapiTest();
if (1)
{
	wx_log("OK:".$url."\n");
	$post = $HTTP_RAW_POST_DATA;
	wx_log("query:".$post."\n");
	$content;
	$from;
	$to;
	$type = intoq($content, $to, $from);
	if (strcmp ($type, "event") === 0)
		do_echo($to, $from);
	exit;
}
else
	wx_log("ERROR:".$url."\n");

?>
