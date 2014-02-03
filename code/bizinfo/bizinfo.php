<?php 
require_once($ROOTDIR."log/log.php");
define ("Q_BASE" , "self_test");
$initflag = 0;
$bizinfo = array();

function init_bizinfo($dblink)
{
	global $bizinfo;

	$result = mysql_query("select bizname, username, passwd, status from open_biz", $dblink);
	if ($result === false)
	{
		runlog("query fakeid from wx_username bizname is null:".mysql_error());
		return $flag;
	}
	while($row=mysql_fetch_array($result)) 
	{
		$v = $row[1]."|".$row[2]."|".$row[3];
		$bizinfo[$row[0]] = $v;
	}
	mysql_free_result($result);
	$initflag = 1;
}

function get_biz_info($bizname, &$username, &$passwd, $dblink)
{
	global $bizinfo;
	$v = $bizinfo[$bizname];
	if (strlen($v) < 10)
	{
		init_bizinfo($dblink);
		$v = $bizinfo[$bizname];
	}

	$pos = strpos($v, "|");
	if ($pos === false)
	{
		runlog("ERR info $v!");
		return false;
	}

	$username = substr($v, 0, $pos);

	$pos1 = strpos($v, "|", $pos+1);
	if ($pos1 === false)
	{
		runlog("ERR info $v!");
		return false;
	}

	$passwd = substr($v, $pos+1, $pos1 - $pos -1);
	return true;
}
?>
