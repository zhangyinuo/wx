<?php
session_start();

$ROOTDIR=dirname(__FILE__)."/../../code/";
$CURDIR=dirname(__FILE__);
define("TOKEN", "self_test");
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
		$content = $postObj->Content;
		$bizname = bizname;
		$msg = $bizname."&&".$fromUsername."&&".$time."&&".$content;
		msg_send($subq, 1, $msg);
	}
}

function do_echo($from, $to)
{
	$bizname = bizname;
	$msg = get_content($bizname, "filename");
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
	$cmd = substr($c, 0, 1);
	switch ($cmd)
	{
	case '2':
	case '3':
	case '4':
	case '5':
	case 'u':
	case 'o':
	case 's':
	case 'c':
	case 'l':
		return;

	default:
		do_echo($from, $to);
	}
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
	intoq($content, $to, $from);
	if (strlen($content) > 0)
		do_rsp($content, $to, $from);
	exit;
}
else
	wx_log("ERROR:".$url."\n");

?>
