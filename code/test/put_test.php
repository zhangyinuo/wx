<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."queue/queue.php");

$pexe = $ROOTDIR."/getlist/phantomjs";
$sendjs = $ROOTDIR."/test/weixin_mass.js";

runlog("$pexe $sendjs $username $passwd $fid $msg");
$fp = popen("$pexe $sendjs $username $passwd $fid '$msg'", "r");
if ($fp)
	pclose($fp);

?>

