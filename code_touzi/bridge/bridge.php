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

function do_rsp_fid($msg, $fid, $bizname)
{
	global $downq;
	$retmsg = $bizname."&&".$fid."&&".$msg;
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

		$fid = get_fid_by_bizname_wx_username($bizname, $wx_username, $dblink);
		if ($fid === "")
		{
			runlog(__FILE__."_".__LINE__.":"."get_fid_by_bizname_wx_username err: ".$bizname.":".$wx_username);
			continue;
		}

		$cmd = substr($msg, 0, 1);
		switch ($cmd)
		{
		case '1':
		case '2':
		case '3':
		case '4':
		case '5':
			$rspstr = get_content($bizname, $cmd);
			do_rsp_fid($rspstr, $fid, $bizname);
			break;

		case 'o':
		case 'O':
			break;
		case 'u':
		case 'U':
			$nickname= "";
			$error = "";
		   	$f = substr($msg, 1, 1);
			if ($f === ':')
			{
				$nickname = substr($msg, 2);
				$error = do_update_nick_name($bizname, $fid, $nickname, $dblink);
			}
			else
				$error = "正确格式: u:聊天名称. :是英文半角的";

			if (strlen($error) > 2)
				$error = "更新错误原因".$error;
			else
				$error = "当前名称".$nickname;
			do_rsp_fid($error, $fid, $bizname);
			break;
		default:
			do_rsp_fid("您需要的功能很快开发", $fid, $bizname);
			$rspstr = get_content($bizname, 1);
			do_rsp_fid($rspstr, $fid, $bizname);
			break;
		}
	}

	mysql_ping($dblink);
	sleep(5);
}
?>
