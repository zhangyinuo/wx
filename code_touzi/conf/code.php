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

function wx_test_parse($src)
{
	$ret = array ();
	return $ret;
};

?>
