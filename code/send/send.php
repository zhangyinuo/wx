<?php 
$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."bizinfo/bizinfo.php");
require_once($ROOTDIR."queue/queue.php");
require_once($ROOTDIR."log/log.php");

$downq = "";
if (init_q($downq, $down_queue_file, "p") === false)
{
	runlog(__FILE__."_".__LINE__.":"."ERR init_ftok $down_queue_file !");
	exit;
}

$type = 0;
while (1)
{
	while(msg_receive($downq, 0, $type, 1024, $message, TRUE, MSG_IPC_NOWAIT)) {
		$username;
		$passwd;
		$fid;
		$msg;

		if (parse_msg_from_queue($message, $username, $passwd, $fid, $msg) === false)
		{
			runlog(__FILE__."_".__LINE__.":"."parse_msg_from_queue err: ".$message);
			continue;
		}
		send_msg_by_fid($username, $passwd, $fid, $msg);
	}

	sleep(5);
}
?>
