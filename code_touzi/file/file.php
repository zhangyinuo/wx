<?php 

$ROOTDIR=dirname(__FILE__)."/../";
$curdir=dirname(__FILE__)."/";

require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."queue/queue.php");

function get_content($bfile)
{
	global $curdir;
	$path = $curdir."file/$bfile/okmsg";
	runlog(__FILE__.":".__LINE__.": file $path prepare!");
	if (file_exists($path))
		return file_get_contents($path);
	else
	{
		runlog(__FILE__.":".__LINE__.": file $path not exist!");
		return false;
	}

}

function process_web_request($fid, $path, $c)
{
	global $wx_down_q;
	$msg = "";

	runlog(__FILE__.":".__LINE__.": $fid prepare!");
	$f = get_content($path."f");
	$msg = sprintf($f, $fid, $c);

	msg_send($wx_down_q, 1, $msg);
}

function process_request($fid, $path, $ret)
{
	global $wx_down_q;
	$msg = "";
	if ($ret != 0)
	{
		runlog(__FILE__.":".__LINE__.": $fid prepare!");
		$f = get_content("bad".$ret."f");
		$c = get_content("bad".$ret);
		$msg = sprintf($f, $fid, $c);
	}
	else
	{
		runlog(__FILE__.":".__LINE__.": $fid prepare!");
		$f = get_content($path."f");
		$c = get_content($path);
		$msg = sprintf($f, $fid, $c);
	}

	msg_send($wx_down_q, 1, $msg);
}
?>

