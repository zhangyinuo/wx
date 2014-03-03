<?php 
$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."bizinfo/bizinfo.php");
require_once($ROOTDIR."queue/queue.php");
require_once($ROOTDIR."log/log.php");

$cmdfile = $ROOTDIR."conf/cmd.txt";
$codefile = $ROOTDIR."conf/code.php";

$parse_array = array();
$exec_array = array();



$subq = "";
if (init_q($subq, $sub_queue_file, "p") === false)
{
	runlog(__FILE__."_".__LINE__.":"."ERR init_ftok $sub_queue_file !");
	exit;
}

$upq = "";
if (init_q($upq, $up_queue_file, "p") === false)
{
	runlog(__FILE__."_".__LINE__.":"."ERR init_ftok $up_queue_file !");
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
	while(msg_receive($subq, 0, $type, 1024, $src, TRUE, MSG_IPC_NOWAIT)) {

		if (parse_msg_from_queue($src, $msg, $wx_username, $time) === false)
		{
			runlog(__FILE__."_".__LINE__.":"."parse_msg_from_queue err: ".$src);
			continue;
		}
		registe_user_2_db($bizname, $wx_username, $time, $dblink, $msg);
		msg_send($upq, 1, $message);
	}

	mysql_ping($dblink);
	sleep(5);
}
?>
