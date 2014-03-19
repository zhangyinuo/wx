<?php 

$ROOTDIR=dirname(__FILE__)."/../";

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
	if ($result === false)
	{
		runlog("query fakeid from wx_username bizname is null:".$wx_username.":".$bizname);
		return $flag;
	}
	$flag = "";
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

function get_fid_by_bizname_wx_username($bizname, $wx_username, $dblink)
{
	$result = mysql_query("select fakeid from wx_userinfo where wx_username = '$wx_username' and bizname = '$bizname' ", $dblink);
	if ($result === false)
	{
		runlog("query fakeid from wx_username bizname is null:".$wx_username.":".$bizname);
		return $flag;
	}
	$flag = "";
	while($row=mysql_fetch_array($result)) 
	{
		$flag = $row[0];
		break;
	}
	mysql_free_result($result);

	return $flag;
}

?>

