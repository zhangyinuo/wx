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
$cb_array = array();

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
		array_push($cb_array, $retarr[0]);
	}
	fclose($fp);
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
	while(msg_receive($wx_sub_q, 0, $type, 1024, $src, TRUE, MSG_IPC_NOWAIT)) {

		$retarr = parse_msg_com($src, "|");
		if (count($retarr) != 3)
		{
			runlog(__FILE__."_".__LINE__.":"."ERR parse_msg_com $src !");
			continue;
		}
		registe_user_2_db($retarr[0], $dblink);

		$msgarr = parse_msg_com($retarr[1], " ");

		runlog(__FILE__."_".__LINE__.":".$src);
		if (in_array($msgarr[0], $cb_array)) 
		{
			runlog(__FILE__."_".__LINE__.":".$src);
			$ncount = intval($args_array[$msgarr[0]]);
			$rcount = intval(count($msgarr));
			if ($ncount === $rcount)
			{
		runlog(__FILE__."_".__LINE__.":".$src);
				$exc_func = $exec_array[$msgarr[0]];
				array_shift($msgarr);
				array_push($msgarr, $dblink);
				call_user_func_array($exc_func, $msgarr);
			}
			else
				runlog(__FILE__."_".__LINE__.":".$src.":".$ncount.":".$rcount);

		}
		else
			runlog(__FILE__."_".__LINE__.":".$src);

	}

	mysql_ping($dblink);
	sleep(5);
}
?>
