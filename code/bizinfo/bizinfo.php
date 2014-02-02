<?php 
require_once($ROOTDIR."log/log.php");
define ("Q_BASE" , "self_test");
$initflag = 0;
$bizinfo = array();

function init_bizinfo($dblink)
{
	global $bizinfo;

	runlog(__FILE__.":".__LINE__);
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
	runlog(__FILE__.":".__LINE__);
}

function get_biz_info($bizname, $username, $passwd, $dblink)
{
	global $bizinfo;
	init_bizinfo($dblink);
	if ($bizinfo[$bizname] === false)
		init_bizinfo($dblink);

	return $bizinfo[$bizname];
}
?>
