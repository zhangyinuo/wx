<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");

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
	$
	while($row=mysql_fetch_array($result)) 
	{
		$flag = true;
		break;
	}
	mysql_free_result($result);
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

	if (get_fid_by_msg($fid, $username, $passwd, $msg) === false)
	{
		runlog(__FILE__."_".__LINE__.":"."get_biz_info:".$bizname.":".$wx_username);
		return;
	}

	insert_replace_fid_wx_username($bizname, $fid, $wx_username, $dblink);
}

?>
