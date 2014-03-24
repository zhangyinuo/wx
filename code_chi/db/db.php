<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."file/file.php");
require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."getlist/getlist.php");

function get_db()
{
	$dbuser = "root";
	$dbpasswd = "123456";
	$dbport = "13306";
	$dbhost = "127.0.0.1";
	$dbdb = "wx_chi";

	$dblink = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpasswd);
	if ($dblink === false)
	{
		runlog(__FILE__."_".__LINE__.":"."Could not query:" . mysql_error());
		runlog(__FILE__."_".__LINE__.":".$dbhost.":".$dbport);
		die("Could not query:" . mysql_error());
	}

	mysql_select_db($dbdb, $dblink);
	if ($dblink === false)
	{
		runlog(__FILE__."_".__LINE__.":"."Could select db:" . mysql_error());
		die("Could select db:" . mysql_error());
	}

	$result = mysql_query("set names utf8;", $dblink);
	if ($result === false)
	{
		runlog(__FILE__."_".__LINE__.":"."Could set names gbk:" . mysql_error());
		die("Could set names gbk:" . mysql_error());
	}

	$result = mysql_query("set autocommit = 1;", $dblink);
	if ($result === false)
	{
		runlog(__FILE__."_".__LINE__.":"."Could set autocommit:" . mysql_error());
		die("Could set autocommit:" . mysql_error());
	}

	return $dblink;
}

function check_is_exist_wx_username($bizname, $wx_username, $dblink)
{
	$result = mysql_query("select status from wx_userinfo where wx_username = '$wx_username' and bizname = '$bizname' ", $dblink);
	$flag = "";
	if ($result === false)
	{
		runlog("query fakeid from wx_username bizname is null:".$wx_username.":".$bizname);
		return $flag;
	}
	while($row=mysql_fetch_array($result)) 
	{
		$flag = $row[0];
		break;
	}
	mysql_free_result($result);

	return $flag;
}

function insert_replace_fid_wx_username($bizname, $fid, $wx_username, $dblink, $status)
{
	$curtime = date("YmdHis");
	$sql = "replace into wx_userinfo values(NULL, '$bizname', '$fid', '$wx_username', 'NULL', 'NULL', '$curtime', '$status', '1', '1', '1', NULL, '$wx_username', 'NULL', NULL)";
	$result = mysql_query($sql, $dblink);
	if ($result === false)
	{
		runlog("insert error ".$wx_username.":".$bizname.":".mysql_error());
		return false;
	}
	return true;
}

function confirm_insert_replace_fid_wx_username($bizname, $fid, $wx_username, $msisdn, $dblink)
{
	$curtime = date("YmdHis");
	$sql = "replace into wx_userinfo values(NULL, '$bizname', '$fid', '$wx_username', 'NULL', 'NULL', '$curtime', '2', '1', '1', '1', '$msisdn', '$wx_username', 'NULL', NULL)";
	$result = mysql_query($sql, $dblink);
	if ($result === false)
	{
		runlog("confirm insert error ".$wx_username.":".$bizname.":".mysql_error());
		return false;
	}
	return true;
}

function do_update_nick_name($bizname, $fid, $nickname, $dblink)
{
	$ret = "";
	$curtime = date("YmdHis");
	$sql = "update wx_userinfo set nickname = '$nickname',  modtime = '$curtime' where bizname = '$bizname' and fakeid = '$fid' ;";
	$result = mysql_query($sql, $dblink);
	if ($result === false)
	{
		runlog("update nickname error ".$fid.":".$bizname.":".$nickname.":".mysql_error());
		return mysql_error();
	}
	return $ret;
}

function registe_user_2_db($bizname, $wx_username, $time, $dblink, $msg)
{
	$status = check_is_exist_wx_username($bizname, $wx_username, $dblink);
	if ($status === '1' || $status === '2')
	{
		runlog(__FILE__."_".__LINE__.":"."check_is_exist_wx_username:".$bizname.":".$wx_username);
		return;
	}

	$username = "";
	$passwd = "";

	if (get_biz_info($bizname, $username, $passwd, $dblink) === false)
	{
		runlog(__FILE__."_".__LINE__.":"."get_biz_info:".$bizname.":".$wx_username);
		return;
	}

	if (strlen($msg) === 0)
	{
		if ($status === '0')
			return;
		if (refresh_fid_biz($bizname, $wx_username, $username, $passwd, $dblink) === false)
			runlog(__FILE__."_".__LINE__.":"."refresh_fid_biz err:".$bizname.":".$wx_username);
		return;
	}

	$fid = "";

	if (get_fid_by_msg($fid, $username, $passwd, $msg, $time, $dblink, $bizname) === false)
	{
		runlog(__FILE__."_".__LINE__.":"."get_fid_by_msg:".$bizname.":".$wx_username);
		return;
	}
	runlog(__FILE__."_".__LINE__.":"."get_fid_by_msg:".$bizname.":".$wx_username."fid=".$fid);

	insert_replace_fid_wx_username($bizname, $fid, $wx_username, $dblink, '1');
}

function is_exist_fakeid($dblink, $fid, $bizname)
{
	$result = mysql_query("select status from wx_userinfo where fakeid = '$fid' and bizname = '$bizname' ", $dblink);
	if ($result === false)
	{
		runlog(__FILE__.":".__LINE__."query fakeid from wx_username bizname is null:".$wx_username.":".$bizname);
		return $flag;
	}
	$count = -1;
	while($row=mysql_fetch_array($result)) 
	{
		$count = $row[0];
		break;
	}
	mysql_free_result($result);

	return $count;
}

function do_djzc($user, $pass, $msisdn, $dblink)
{
	$result = mysql_query("select cbizname from t_biz_info where bizname = '$user' and bizpasswd = '$pass' ", $dblink);
	if ($result === false)
	{
		runlog(__FILE__.":".__LINE__."query fakeid from wx_username bizname is null:".mysql_error());
		return "";
	}
	$cbizname = "";
	while($row=mysql_fetch_array($result)) 
	{
		$cbizname = $row[0];
		break;
	}
	mysql_free_result($result);

	if (strlen($cbizname) > 0)
	{
		$sql = "update t_biz_info set msisdn = '$msisdn', flag = 1 where  bizname = '$user' and bizpasswd = '$pass' ";
		mysql_query ($sql, $dblink);
		return true;
	}

	return false;
}

function get_fid_by_bizname_wx_username($bizname, $wx_username, &$msisdn, $dblink)
{
	$result = mysql_query("select fakeid, msisdn from wx_userinfo where wx_username = '$wx_username' and bizname = '$bizname' ", $dblink);
	if ($result === false)
	{
		runlog("query fakeid from wx_username bizname is null:".$wx_username.":".$bizname);
		return $flag;
	}
	$fid = "";
	while($row=mysql_fetch_array($result)) 
	{
		$fid = $row[0];
		$msisdn = $row[1];
		break;
	}
	mysql_free_result($result);

	return $fid;
}

function get_fid_by_msisdn($bizname, &$fid, $msisdn, $dblink)
{
	$flag = 1;
	$result = mysql_query("select fakeid from wx_userinfo where msisdn = '$msisdn' and bizname = '$bizname' ", $dblink);
	if ($result === false)
	{
		runlog("query fakeid from wx_username bizname is null:".$wx_username.":".$bizname);
		return $flag;
	}
	$fid = "";
	while($row=mysql_fetch_array($result)) 
	{
		$fid = $row[0];
		$flag = 0;
		break;
	}
	mysql_free_result($result);

	return $flag;
}

function get_biz_id($msisdn, &$flag, $dblink)
{
	$result = mysql_query("select id, flag from t_biz_info where msisdn = '$msisdn' ", $dblink);
	if ($result === false)
	{
		runlog(__FILE__.":".__LINE__."query fakeid from wx_username bizname is null:".mysql_error());
		return $flag;
	}
	runlog(__FILE__.":".__LINE__."select id, flag from t_biz_info where msisdn = ".$msisdn);
	$id = "";
	while($row=mysql_fetch_array($result)) 
	{
		$id = $row[0];
		$flag = $row[1];
		break;
	}
	mysql_free_result($result);

	runlog(__FILE__.":".__LINE__."flag:".$flag);
	return $id;
}

function get_biz_msisdn_by_id(&$msisdn, $bizid, $dblink)
{
	$flag = 1;
	$result = mysql_query("select msisdn from t_biz_info where id = '$bizid' ", $dblink);
	if ($result === false)
	{
		runlog(__FILE__.":".__LINE__."query fakeid from wx_username bizname is null:".mysql_error());
		return $flag;
	}
	while($row=mysql_fetch_array($result)) 
	{
		$msisdn = $row[0];
		$flag = 0;
		break;
	}
	mysql_free_result($result);

	return $flag;
}

function clear_wx_step($fid, $dblink)
{
	$sql = "replace into t_select_info value ('$fid', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0)"; 
	$result = mysql_query($sql, $dblink); 
}

function get_all_biz($dblink)
{
	$rspstr = "";
	$result = mysql_query("select id, cbizname from t_biz_info where flag = 1 ", $dblink);
	if ($result === false)
	{
		runlog(__FILE__.":".__LINE__."query fakeid from wx_username bizname is null:".mysql_error());
		return $rspstr;
	}
	while($row=mysql_fetch_array($result)) 
	{
		$sub = $row[0].":".$row[1]."\n";
		$rspstr = $rspstr.$sub;
	}
	mysql_free_result($result);
	return $rspstr;
}

function get_last_path($fid, &$idx, &$bizid, &$path, $cur, $dblink)
{
	$result = mysql_query("select * from t_select_info where fakeid = '$fid' ", $dblink);
	if ($result === false)
	{
		runlog(__FILE__.":".__LINE__."query fakeid from wx_username bizname is null:".mysql_error());
		return false;
	}
	$path = "";

	$retval = 0;
	$curtime = time();
	while($row=mysql_fetch_array($result)) 
	{
		if ($curtime - intval($row[7]) < 600)
		{
			if ($row[8] <= 0)
			{
				runlog(__FILE__.":".__LINE__);
				$retval = "intel_error";
			}
			else
			{
				runlog(__FILE__.":".__LINE__);
				for ($idx = 0; $idx < $row[8]; $idx++)
				{
					$path = $path."/".$row[$idx+1];
				}
				$path = $path."/".$cur;
			}
		}
		else if ($row[8] == 0)
		{
			runlog(__FILE__.":".__LINE__);
			$path = $cur;
		}
		else
		{
			runlog(__FILE__.":".__LINE__);
			$retval = "dc_ot";
		}
		$idx = $row[8];
		$bizid = $row[1];
		break;
	}
	mysql_free_result($result);

	return $retval;
}

function update_idx_select($fid, $msg, $idx, $dblink)
{
	$idx++;
	$col = "step".$idx;

	$curtime = time();
	$sql = "update t_select_info set lastindex = $idx, lasttime = $curtime, $col = '$msg' where fakeid = '$fid'";

	mysql_query($sql, $dblink);
}

function dispatch_to_biz($msisdn, $bizid, $rspstr, $username, $passwd, $dblink)
{
	$bizmsisdn = "";
	if (get_biz_msisdn_by_id(&$bizmsisdn, $bizid, $dblink))
	{
		runlog(__FILE__.":".__LINE__."query fakeid from wx_username bizname is null:".mysql_error());
		return 1;
	}

	$fid = "";
	if (get_fid_by_msisdn("self_test", &$fid, $bizmsisdn, $dblink))
	{
		runlog(__FILE__.":".__LINE__."query fakeid from wx_username bizname is null:".mysql_error());
		return 1;
	}

	$rspstr = get_content("self_test", "dc_biz_pre").$msisdn.":\n".$rspstr;
	do_rsp_fid($rspstr, $fid, $username, $passwd);
}

?>

