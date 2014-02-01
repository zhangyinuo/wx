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

function registe_user_2_db($bizname, $wx_username, $time, $dblink)
{
	if (check_is_exist_wx_username($bizname, $wx_username))
	{
		runlog(__FILE__."_".__LINE__.":"."check_is_exist_wx_username:".$bizname.":".$wx_username);
		return;
	}
}

?>

