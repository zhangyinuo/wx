<?php
/**
 * 微信扩展接口测试
 */
require_once(dirname(__FILE__) ."/queue/common.php");

function logdebug($text){
	file_put_contents('/home/jingchun.zhang/svn/sys_dev/net_monitor/wx/data/log.txt',$text."\n",FILE_APPEND);		
};

$downq = "";
if (init_q($downq, $down_queue_file1, "p") === false)
{
	runlog("ERR init_ftok $down_queue_file1!");
	exit;
}
runlog("init downq ok:".$downq);

function push_msg($fakeid, $msg)
{
	global $downq;
	$msgs = $fakeid."&&".$msg;
	msg_send($downq, 1, $msgs);
}

function get_oper_idc($mondb, $ip, &$idc, &$oper)
{
	$result = mysql_query("select idcid, groupid from t_server_base_info where status = 1 and first_ip = '$ip';", $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	$ret = false;
	while($row=mysql_fetch_array($result)) 
	{
		$ret = true;
		$idc = $row[0];
		$oper = $row[1];
		break;
	}
	mysql_free_result($result);
	return $ret;
}

function do_sub_process($mondb, $msg, $idc, $oper, $flag)
{
	$result = mysql_query("select fakeid, subscribe_idc, subscribe_oper from weixin_push_msg where status = '1'", $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	$idc = "|".$idc."|";
	$oper = "|".$oper."|";
	while($row=mysql_fetch_array($result)) 
	{
		if ($flag)
		{
			if (stristr($row[1], "ALL") === false)
			{
				if (strstr($row[1], $idc) === false)
					continue;
			}
			if (stristr($row[2], "ALL") === false)
			{
				if (strstr($row[2], $oper) === false)
					continue;
			}
		}
		push_msg($row[0], $msg);
	}
	mysql_free_result($result);
}


function do_process($mondb)
{
	$table = "t_alarm_msg_".date('Ym');
	$result = mysql_query("select ip, alarmmsg from $table where ilevel = 0 and flag = 0;", $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	$idc = "";
	$oper = "";
	while($row=mysql_fetch_array($result)) 
	{
		if (get_oper_idc($mondb, $row[0], $idc, $oper) === true)
			do_sub_process($mondb, $row[1], $idc, $oper, 1);
	}
	mysql_free_result($result);

	$result = mysql_query("select alarmmsg from $table where rule_id = 134223827 and flag = 0;", $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	while($row=mysql_fetch_array($result)) 
	{
		do_sub_process($mondb, $row[0], $idc, $oper, 0);
	}
	mysql_free_result($result);
}

function update_send($mondb, $username, $time)
{
	$result = mysql_query("update t_person_info set lastsend = '$time' where person = '$username';", $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}
}

function check_fakeid($fakeid, $mondb)
{
	$result = mysql_query("select fakeid from weixin_push_msg where fakeid = '$fakeid';", $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	$ret = false;

	while($row=mysql_fetch_array($result)) 
	{
		$ret = true;
		break;
	}
	mysql_free_result($result);
	return $ret;
}

function get_fakeid($username, $mondb)
{
	$result = mysql_query("select fakeid from weixin_push_msg where status = '1' and username = '$username';", $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	$ret = false;

	while($row=mysql_fetch_array($result)) 
	{
		$ret = $row[0];
		break;
	}
	mysql_free_result($result);
	return $ret;
}

function send_sign_msg($fakeid, $msg)
{
	push_msg($fakeid, $msg);
}

function check_unsign($mondb)
{
	return;
	$file = "/home/jingchun.zhang/svn/sys_dev/net_monitor/wx/getlist/userlist";
	$url = "https://56.com/ops/index.htm?fakeid=";

	$handle = @fopen($file, "r");
	if ($handle) {
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			$fid = substr($buffer, 0, -1);
			if (strlen($fid) < 3)
				continue;
			if (check_fakeid($fid, $mondb) === false)
			{
				send_sign_msg($fid, '请注册'.":$url".$fid);
				runlog("send $fid sign:".date("Y-m-d H:i:s"));
			}
		}
		fclose($handle);
	}
}

function check_sign($mondb)
{
	$result = mysql_query("select person, lastcheck from t_person_info where (TIMESTAMPDIFF(SECOND,lastcheck,  now()) > 604800 and lastsend <= lastcheck ) or (lastsend >= lastcheck and (TIMESTAMPDIFF(SECOND,lastsend,  now())> 86400)) ;",  $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}


	$check_list = array();

	while($row=mysql_fetch_array($result)) 
	{
		$fakeid = get_fakeid($row[0], $mondb);
		if ($fakeid === false)
			continue;
		$check_list[$row[0]] = $fakeid;
	}
	mysql_free_result($result);

	foreach ($check_list as $key =>$val)
	{
		send_sign_msg($val, '为保持告警通道畅通，请签到，直接回复即可');
		runlog("send $key sign:".date("Y-m-d H:i:s"));
		update_send($mondb, $key, date("Y-m-d H:i:s"));
	}
}

$monlnk = mysql_connect('211.151.181.209:49710', 'php_moni', 'InXzme5lCi0rU6VW');
if ($monlnk === false)
{
	runlog("Could not query:" . mysql_error());
	die("Could not query:" . mysql_error());
}

mysql_select_db('monitor', $monlnk);
if ($monlnk === false)
{
	runlog("Could select db:" . mysql_error());
	die("Could select db:" . mysql_error());
}

$result = mysql_query("set names utf8;", $monlnk);
if ($result === false)
{
	runlog("Could set names gbk:" . mysql_error());
	die("Could set names gbk:" . mysql_error());
}

do_process($monlnk);
check_sign($monlnk);
check_unsign($monlnk);
