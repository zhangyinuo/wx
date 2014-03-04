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

function wx_test_exe($srcarr)
{
	runlog(__FILE__."_".__LINE__);
};

function wx_test_parse($src)
{
	$ret = array ();
	return $ret;
};

?>
