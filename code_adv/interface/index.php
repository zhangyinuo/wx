<?php
session_start();
require_once("/home/jingchun.zhang/svn/sys_dev/net_monitor/weixin/root/wxtest/wx_sample.php");
require_once("/diska/sys_dev/wx/wx/code_adv/queue/queue.php");
require_once("/diska/sys_dev/wx/wx/code_adv/file/file.php");
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
header("Content-Type: text/html; charset=gb2312");

define ('bizname', "self_test");

$subq = "";
if (init_q($subq, $sub_queue_file, "p") === false)
{
	open_log(__FILE__."_".__LINE__.":"."ERR init_ftok $sub_queue_file !");
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
		if (strcmp ($msgType, "event") === 0)
		{
			open_log("echo:".__FILE__.":".__LINE__."\n");
			$content = $postObj->EventKey;
		}
		else
		{
			open_log("echo:".__FILE__.":".__LINE__."\n");
			$content = $postObj->Content;
		}
		$bizname = bizname;
		$msg = $bizname."&&".$fromUsername."&&".$time."&&".$content."&&".$msgType;
		msg_send($subq, 1, $msg);

		return $msgType;
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
	open_log("echo:".$msg."\n");
	echo $msg;
}

function do_rsp($c, $from, $to)
{
	open_log("echo:".__FILE__.":".__LINE__."\n");
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

function do_rsp_key($c, $from, $to)
{
	open_log("echo:".__FILE__.":".__LINE__."\n");
	$bizname = bizname;
	$msg = get_content($bizname, $c);
	$pre = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>";
	$premsg = sprintf($pre, $to, $from, time());
	$msg = $premsg.$msg;
	open_log("echo:".$msg."\n");
	echo $msg;
}

$url = $_SERVER['REQUEST_URI'];
$wechatObj = new wechatCallbackapiTest();
if ($wechatObj->valid() === true)
{
	open_log("OK:".$url."\n");
	$post = $HTTP_RAW_POST_DATA;
	open_log("query:".$post."\n");
	$content;
	$from;
	$to;
	$type = intoq($content, $to, $from);
	if (strcmp ($type, "event") === 0)
	{
		open_log("echo:".__FILE__.":".__LINE__."\n");
		if (strlen($content) > 3)
			do_rsp_key($content, $to, $from);
	}
	else
	{
		open_log("echo:".__FILE__.":".__LINE__."\n");
		if (strlen($content) > 0)
			do_rsp($content, $to, $from);
	}
	exit;
}
else
	open_log("ERROR:".$url."\n");

?>