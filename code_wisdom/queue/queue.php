<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");

$sub_queue_file = $ROOTDIR."ftok/sub_queue_self_test";
$up_queue_file = $ROOTDIR."ftok/up_queue_self_test";
$down_queue_file = $ROOTDIR."ftok/down_queue_self_test";
$dispatch_queue_file = $ROOTDIR."ftok/dispatch_queue_self_test";

$wx_sub_q = "";
$wx_up_q = "";
$wx_down_q = "";
$wx_dispatch_q = "";

function q_init(&$q, $file, $p)
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

q_init($wx_sub_q, $sub_queue_file, "p");

q_init($wx_up_q, $up_queue_file, "p");

q_init($wx_down_q, $down_queue_file, "p");

q_init($wx_dispatch_q, $dispatch_queue_file, "p");

?>

