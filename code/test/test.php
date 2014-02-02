<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."bizinfo/bizinfo.php");

$dblink= get_db();
if ($dblink === false)
{
	runlog(__FILE__."_".__LINE__.":"."Could not query:" . mysql_error());
	die("Could not query:" . mysql_error());
}

$v = get_biz_info("self_test", "", "", $dblink);

echo "$v\n";
?>

