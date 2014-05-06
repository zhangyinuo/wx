<?php 

$ROOTDIR=dirname(__FILE__)."/../";
require_once($ROOTDIR."token/menu.php");
$dblink = get_db();

if ($dblink === false)
{
	echo "get db error!\n";
	exit;
}
update_menu("self_test", $dblink)
?>

