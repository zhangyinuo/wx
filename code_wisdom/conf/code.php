<?php 
$ROOTDIR=dirname(__FILE__)."/../";
require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."log/log.php");

function wx_reg_exe($srcarr)
{
};

function wx_reg_parse($src)
{
	$ret = array ();
	return $ret;
};

function wx_test_exe($msg, $dblink)
{
	$result = mysql_query("select wx_username from t_wx_info ", $dblink);
	if ($result === false)
	{
		runlog("query wx_username from t_wx_info is nul:");
		return false;
	}
	while($row=mysql_fetch_array($result)) 
	{
		runlog(__FILE__."_".__LINE__.":".$row[0]);
	}
	mysql_free_result($result);
	runlog(__FILE__."_".__LINE__.":".$msg);
};

function location_exe($wx_username, $func, $x, $y, $lable, $mtime, $dblink)
{
	$sql = "insert into t_wx_location values (NULL, '$wx_username', '$mtime', '$x', '$y', '$lable') ";
	$result = mysql_query($sql, $dblink);
	if ($result === false)
	{
		runlog("location_exe err ".mysql_error());
		process_request($wx_username, "", 5);
		return false;
	}
	process_request($wx_username, "", 4);
};

function record_select($wx_username, $k, $dblink)
{
	$curtime = date("YmdHis");
	$sql = "insert into t_wx_data values (NULL, '$wx_username', '$curtime', '$k') ";
	runlog("record ".$sql);
	$result = mysql_query($sql, $dblink);
	if ($result === false)
		runlog("record err ".mysql_error());
	$sql = "update t_wx_info set atime = '$curtime' where wx_username = '$wx_username' ";
	$result = mysql_query($sql, $dblink); 
};

function record_voice($wx_username, $func, $k, $mtime, $dblink)
{
	$curtime = date("YmdHis");
	$sql = "insert into t_wx_voice values (NULL, '$wx_username', '$curtime', 0, '$k') ";
	runlog("record ".$sql);
	$result = mysql_query($sql, $dblink);
	if ($result === false)
		runlog("record err ".mysql_error());
	$sql = "update t_wx_info set atime = '$curtime' where wx_username = '$wx_username' ";
	$result = mysql_query($sql, $dblink); 
};

function wx_test_parse($src)
{
	$ret = array ();
	return $ret;
};

?>
