<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");

$sub_queue_file = $ROOTDIR."ftok/sub_queue_self_test";

function init_q(&$q, $file, $p)
{
	if (touch($file))
	{
		$key = ftok($file, $p);
		$q = msg_get_queue($key, 0777);
		return true;
	}
	runlog("ERR to touch $file!");
	return false;
}

function parse_msg_from_queue($msg, &$bizname, &$wx_username, &$time)
{
	$pos = strpos($msg, "&&", 0);
	if ($pos === false)
		return false;
	$bizname = substr($msg, 0, $pos);

	$pos1 = strpos($msg, "&&", $pos + 2);
	if ($pos1 === false)
		return false;
	$wx_username = substr($msg, $pos+2, $pos1 - $pos -2);

	$time = substr($msg, $pos1 + 2);
	return true;
}

?>

