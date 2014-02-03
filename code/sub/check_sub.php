<?php 
$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."bizinfo/bizinfo.php");
require_once($ROOTDIR."queue/queue.php");
require_once($ROOTDIR."log/log.php");

$subq = "";
if (init_q($subq, $sub_queue_file, "p") === false)
{
	runlog(__FILE__."_".__LINE__.":"."ERR init_ftok $sub_queue_file !");
	exit;
}

$dblink= get_db();
if ($dblink === false)
{
	runlog(__FILE__."_".__LINE__.":"."Could not query:" . mysql_error());
	die("Could not query:" . mysql_error());
}

$type = 0;
while (1)
{
	while(msg_receive($subq, 0, $type, 1024, $message, TRUE, MSG_IPC_NOWAIT)) {
		$bizname = "";
		$wx_username = "";
		$time = "";
		$msg = "";

		if (parse_msg_from_queue($message, $bizname, $wx_username, $time, $msg) === false)
		{
			runlog(__FILE__."_".__LINE__.":"."parse_msg_from_queue err: ".$message);
			continue;
		}
		echo "$message\n";
		echo "$bizname $wx_username $time $msg\n";
		registe_user_2_db($bizname, $wx_username, $time, $dblink, $msg);
	}

	mysql_ping($dblink);
	sleep(5);
}
?>
