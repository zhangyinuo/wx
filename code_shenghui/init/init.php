<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."queue/queue.php");

$subq = "";
if (init_q($subq, $sub_queue_file, "p") === false)
{
	runlog(__FILE__."_".__LINE__.":"."ERR init_ftok $sub_queue_file !");
	exit;
}

function parse_rec($rec)
{
	global $subq;
	$postObj = simplexml_load_string($rec, 'SimpleXMLElement', LIBXML_NOCDATA);
	$wx_username = $postObj->FromUserName;
	$time = $postObj->CreateTime;
	$content = $postObj->Content;

	$msg = "self_test&&".$wx_username."&&".$time."&&".$content;

	msg_send($subq, 1, $msg);
}

$str = file_get_contents("./phplog");

$s = 0;
$e = 0;

while (1)
{
	$s = strpos($str, "<xml>", $e);
	if ($s === false)
		break;
	$e = strpos($str, "</xml>", $s);
	if ($e === false)
		break;
	$e += 6;
	$rec = substr($str, $s, $e - $s);

	parse_rec($rec);
}
?>

