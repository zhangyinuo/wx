<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");

$sub_queue_file = $ROOTDIR."ftok/sub_queue_self_test";
$up_queue_file = $ROOTDIR."ftok/up_queue_self_test";
$down_queue_file = $ROOTDIR."ftok/down_queue_self_test";

function init_q(&$q, $file, $p)
{
	runlog(__FILE__.":".__LINE__.":".$file);
	if (touch($file))
	{
	runlog(__FILE__.":".__LINE__.":".$file);
		$key = ftok($file, $p);
	runlog(__FILE__.":".__LINE__.":".$file);
		$q = msg_get_queue($key, 0666);
	runlog(__FILE__.":".__LINE__.":".$file);
		return true;
	}
	runlog(__FILE__.":".__LINE__.":".$file);
	runlog("ERR to touch $file!");
	return false;
}

function parse_msg_from_queue($msg, &$bizname, &$wx_username, &$time, &$retmsg)
{
	$pos = strpos($msg, "&&", 0);
	if ($pos === false)
	{
		runlog(__FILE__.":".__LINE__);
		return false;
	}
	$bizname = substr($msg, 0, $pos);

	$pos1 = strpos($msg, "&&", $pos + 2);
	if ($pos1 === false)
	{
		runlog(__FILE__.":".__LINE__);
		return false;
	}
	$wx_username = substr($msg, $pos+2, $pos1 - $pos -2);

	$pos2 = strpos($msg, "&&", $pos1 + 2);
	if ($pos2 === false)
		$time = substr($msg, $pos1 + 2);
	else
	{
		$time = substr($msg, $pos1 + 2, $pos2 - $pos1 - 2);
		$retmsg = substr($msg, $pos2 + 2);
	}
	return true;
}

function parse_msg_from_queue2($msg, &$bizname, &$wx_username, &$time, &$retmsg, &$expire)
{
	if (parse_msg_from_queue($msg, $bizname, $wx_username, $time, $retmsg) === false)
	{
		runlog(__FILE__.":".__LINE__);
		return false;
	}

	if (strlen($retmsg) === 0)
	{
		runlog(__FILE__.":".__LINE__);
		return false;
	}

	$pos = strpos($retmsg, "&&", 0);
	if ($pos === false)
	{
		runlog(__FILE__.":".__LINE__);
		return false;
	}
	$expire = substr($retmsg, $pos+2);
	$retmsg = substr($retmsg, 0, $pos);
	return true;
}

?>

