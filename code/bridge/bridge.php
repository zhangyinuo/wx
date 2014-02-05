<?php 
$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."bizinfo/bizinfo.php");
require_once($ROOTDIR."queue/queue.php");
require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."file/file.php");

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
	while(msg_receive($upq, 0, $type, 1024, $message, TRUE, MSG_IPC_NOWAIT)) {
		$bizname = "";
		$wx_username = "";
		$time = "";
		$msg = "";

		if (parse_msg_from_queue($message, $bizname, $wx_username, $time, $msg) === false)
		{
			runlog(__FILE__."_".__LINE__.":"."parse_msg_from_queue err: ".$message);
			continue;
		}

		$fid = get_fid_by_bizname_wx_username($bizname, $wx_username, $dblink);
		if ($fid === "")
		{
			runlog(__FILE__."_".__LINE__.":"."get_fid_by_bizname_wx_username err: ".$bizname.":".$wx_username);
			continue;
		}

		$cmd = substr($msg, 0, 1);
		switch ($cmd)
		{
		case '2':
		case '3':
		case '4':
		case '5':
			$rspstr = get_content($bizname, $cmd);


		default:
			do_de($from, $to);
		}
	}

	mysql_ping($dblink);
	sleep(5);
}
?>
