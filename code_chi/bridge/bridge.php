<?php 
$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."bizinfo/bizinfo.php");
require_once($ROOTDIR."queue/queue.php");
require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."file/file.php");
require_once($ROOTDIR."common/common.php");

$upq = "";
if (init_q($upq, $up_queue_file, "p") === false)
{
	runlog(__FILE__."_".__LINE__.":"."ERR init_ftok $up_queue_file !");
	exit;
}

$downq = "";
if (init_q($downq, $down_queue_file, "p") === false)
{
	runlog(__FILE__."_".__LINE__.":"."ERR init_ftok $down_queue_file !");
	exit;
}

$dblink= get_db();
if ($dblink === false)
{
	runlog(__FILE__."_".__LINE__.":"."Could not query:" . mysql_error());
	die("Could not query:" . mysql_error());
}

function do_rsp_fid($msg, $fid, $username, $passwd)
{
	global $downq;
	$retmsg = $username."&&".$passwd."&&".$fid."&&".$msg;
	msg_send($downq, 1, $retmsg);
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

		$msisdn = "";
		$fid = get_fid_by_bizname_wx_username($bizname, $wx_username, $msisdn, $dblink);
		if ($fid === "")
		{
			runlog(__FILE__."_".__LINE__.":"."get_fid_by_bizname_wx_username err: ".$bizname.":".$wx_username);
			continue;
		}

		$username = "";
		$passwd = "";

		if (get_biz_info($bizname, $username, $passwd, $dblink) === false)
		{
			runlog(__FILE__."_".__LINE__.":"."get_biz_info:".$bizname.":".$wx_username);
			continue;
		}

		if (strlen($msg) === 11 && is_numeric($msg) === true)
		{
			$msisdn = $msg;
			confirm_insert_replace_fid_wx_username($bizname, $fid, $wx_username, $msisdn, $dblink);
			$rspstr = get_content($bizname, "DH_OK");
			do_rsp_fid($rspstr, $fid, $username, $passwd);
			continue;
		}

		if (strncmp($msg, "djzc", 4) === 0)
		{
			$retarr = parse_msg_com($msg, " ");
			if (count($retarr) != 4)
			{
				$rspstr = get_content($bizname, "djzc_error");
				do_rsp_fid($rspstr, $fid, $username, $passwd);
				continue;
			}
			if (do_djzc($retarr[1], $retarr[2], $retarr[3], $dblink) === true)
			{
				$rspstr = get_content($bizname, "djzc_ok");
				do_rsp_fid($rspstr, $fid, $username, $passwd);
				continue;
			}
			else
			{
				$rspstr = get_content($bizname, "djzc_unok");
				do_rsp_fid($rspstr, $fid, $username, $passwd);
				continue;
			}
		}

		if (strncmp($msg, "cdts", 4) === 0)
		{
			if ($msisdn === "")
			{
				$rspstr = get_content($bizname, "DH_FIRST");
				do_rsp_fid($rspstr, $fid, $username, $passwd);
				continue;
			}
			$flag = "";
			$bizid = get_biz_id($msisdn, $flag, $dblink);
			if ($flag === "")
			{
				$rspstr = get_content($bizname, "djzc_first");
				do_rsp_fid($rspstr, $fid, $username, $passwd);
				continue;
			}

			if ($flag === 0)
			{
				$rspstr = get_content($bizname, "djzc_xufei");
				do_rsp_fid($rspstr, $fid, $username, $passwd);
				continue;
			}
			$pos = strpos($msg, "\n");
			if ($pos === false)
			{
				$rspstr = get_content($bizname, "cdts_error");
				do_rsp_fid($rspstr, $fid, $username, $passwd);
				continue;
			}
			$msg = substr($msg, $pos +1);
			$idx = 0;
			do_cdxx($bizid, $idx, $cdxx);
			$idx++;
			while (1)
			{
				$pos = strpos($msg, "\n");
				if ($pos === false)
					break;
				$submsg = substr($msg, 0, $pos);
				$cdxx = $submsg;
				do_cdxx($bizid, $idx, $cdxx);
				$idx++;
				$msg = substr($msg, $pos + 1);
			}

			$cdxx = $msg;
			do_cdxx($bizid, $idx, $cdxx);
		}

	}

	mysql_ping($dblink);
	sleep(5);
}
?>
