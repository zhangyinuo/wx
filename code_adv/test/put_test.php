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

$msg = "self_test&&oMj31t2-QJyDQ8U_Eix5btP3LwYo&&1391347447&&faint";

msg_send($subq, 1, $msg);

?>

