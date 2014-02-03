<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."getlist/getlist.php");

function get_db()
{
	$dbuser = "root";
	$dbpasswd = "123456";
	$dbport = "23306";
	$dbhost = "14.17.117.32";
	$dbdb = "wx";

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

	return $dblink;
}

function check_is_exist_wx_username($bizname, $wx_username, $dblink)
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

	if (strlen($flag) > 0)
	{
		$result = mysql_query("update wx_userinfo set status = '2' where wx_username = '$wx_username' and bizname = '$bizname' ", $dblink);
		return true;
	}

	return false;
}

function insert_replace_fid_wx_username($bizname, $fid, $wx_username, $dblink)
{
	$curtime = date("YmdHis");
	$sql = "replace into wx_userinfo values(NULL, '$bizname', '$fid', 'NULL', 'NULL', 'NULL', '$curtime', '1', '1', '1', '1', NULL, '$wx_username', 'NULL', NULL)";
	$result = mysql_query($sql, $dblink);
	if ($result === false)
	{
		runlog("insert error ".$wx_username.":".$bizname.":".mysql_error());
		return false;
	}
	return true;
}

function registe_user_2_db($bizname, $wx_username, $time, $dblink, $msg)
{
	if (check_is_exist_wx_username($bizname, $wx_username, $dblink) === true)
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

	insert_replace_fid_wx_username($bizname, $fid, $wx_username, $dblink);
}

function is_exist_fakeid($dblink, $fid, $bizname)
{
	$result = mysql_query("select count(1) from wx_userinfo where fakeid = '$fid' and bizname = '$bizname' ", $dblink);
	if ($result === false)
	{
		runlog(__FILE__.":".__LINE__."query fakeid from wx_username bizname is null:".$wx_username.":".$bizname);
		return $flag;
	}
	$count = 0;
	while($row=mysql_fetch_array($result)) 
	{
		$count = $row[0];
		break;
	}
	mysql_free_result($result);

	return $count;
}

?>

