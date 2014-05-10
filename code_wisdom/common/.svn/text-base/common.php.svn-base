<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");

function parse_msg_com($msg, $del)
{
	$ret = array();
	$spos = 0;
	$epos = 0;
	while (1)
	{
		$epos = strpos($msg, $del, $spos);
		if ($epos === false)
		{
			$submsg = substr($msg, $spos);
			array_push($ret, $submsg);
			return $ret;
		}
		$submsg = substr($msg, $spos, $epos - $spos);
		array_push($ret, $submsg);
		$spos = $epos + strlen($del);
	}
}

?>

