<?php
session_start();
$ROOTDIR=dirname(__FILE__)."/../../code_touzi/";
$CURDIR=dirname(__FILE__);
define("TOKEN", "caidan");
require_once($CURDIR."/wx_sample.php");
require_once($ROOTDIR."/queue/queue.php");
require_once($ROOTDIR."/file/file.php");
header("Content-Type: text/html; charset=gb2312");

function intoq(&$content, &$toUsername, &$fromUsername)
{
	//get post data, May be due to the different environments
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

	global $wx_sub_q;
	//extract post data
	if (!empty($postStr)){

		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$toUsername = $postObj->ToUserName;
		$fromUsername = $postObj->FromUserName;
		$time = $postObj->CreateTime;
		$msgType = $postObj->MsgType;
		if (strcmp ($msgType, "event") === 0)
		{
			wx_log("echo:".__FILE__.":".__LINE__."\n");
			$content = $postObj->EventKey;
		}
		else
		{
			wx_log("echo:".__FILE__.":".__LINE__."\n");
			$content = $postObj->Content;
		}
		$msg = $fromUsername."|".$content."|".$time;
		if (msg_send($wx_sub_q, 1, $msg) === false)
			wx_log("ERROR:".__FILE__.":".__LINE__."\n");

		return $msgType;
	}
}

function do_rsp_key($c, $from, $to)
{
	wx_log("echo:".__FILE__.":".__LINE__."\n");
	$msg = get_content($c);
	$pre = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>";
	$premsg = sprintf($pre, $to, $from, time());
	$msg = $premsg.$msg;
	wx_log("echo:".$msg."\n");
	echo $msg;
}

$url = $_SERVER['REQUEST_URI'];
$wechatObj = new wechatCallbackapiTest();
//if ($wechatObj->valid() === true)
if (1)
{
	wx_log("OK:".$url."\n");
	$post = $HTTP_RAW_POST_DATA;
	wx_log("query:".$post."\n");
	$content;
	$from;
	$to;
	$type = intoq($content, $to, $from);
	exit;
}
else
	wx_log("ERROR:".$url."\n");

?>
