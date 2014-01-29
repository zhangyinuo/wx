<?php
session_start();
require_once("/home/jingchun.zhang/svn/sys_dev/net_monitor/weixin/root/dev/wx_sample.php");
require_once("/home/jingchun.zhang/svn/sys_dev/net_monitor/wx_dev/queue/common.php");
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
header("Content-Type: text/html; charset=gb2312");

$upq = "";
if (init_q($upq, $up_queue_file1, "p") === false)
{
	open_log("ERR init_ftok $up_queue_file1!");
	exit;
}

$url = $_SERVER['REQUEST_URI'];
$wechatObj = new wechatCallbackapiTest();
if ($wechatObj->valid() === true)
{
	open_log("OK:".$url."\n");
	$post = $HTTP_RAW_POST_DATA;
	open_log("query:".$post."\n");
	put_up_msg($post, $upq);
}
else
	open_log("ERROR:".$url."\n");

?>
