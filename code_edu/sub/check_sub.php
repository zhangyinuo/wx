<?php 
$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."common/common.php");
require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."bizinfo/bizinfo.php");
require_once($ROOTDIR."queue/queue.php");
require_once($ROOTDIR."log/log.php");

$cmdfile = $ROOTDIR."conf/cmd.txt";
require_once($ROOTDIR."conf/code.php");

$parse_array = array();
$exec_array = array();
$args_array = array();

$fp = fopen($cmdfile, "r");
if ($fp)
{
	while (!feof($fp))
	{
		$bufs = fgets($fp);
		if (substr($bufs, 0, 1) === "#")
			continue;
		if (strlen($bufs) < 5)
			continue;
		$bufs = str_replace(PHP_EOL, '', $bufs);

		$retarr = parse_msg_com($bufs, "|");
		if (count($retarr) != 4)
		{
			runlog(__FILE__."_".__LINE__.":"."ERR parse_msg_com $bufs !");
			exit;
		}
		$args_array[$retarr[0]] = $retarr[1];
		$parse_array[$retarr[0]] = $retarr[2];
		$exec_array[$retarr[0]] = $retarr[3];
	}
	fclose($fp);
}

print_r($args_array);
print_r($parse_array);
print_r($exec_array);
exit;

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
