<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");

$sub_queue_file = $ROOTDIR."ftok/sub_queue_self_test";
$up_queue_file = $ROOTDIR."ftok/up_queue_self_test";
$down_queue_file = $ROOTDIR."ftok/down_queue_self_test";

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

function parse_msg_from_queue($msg, &$bizname, &$fid, &$retmsg)
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
	$fid = substr($msg, $pos+2, $pos1 - $pos -2);

	$retmsg = substr($msg, $pos1 + 2);
}

?>

