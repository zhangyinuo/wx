<?php 
$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."db/db.php");

function is_match($content, $time, $infile)
{
	$needle = $time;
	runlog ("$time $content\n");
	$result = file_get_contents($infile);
	$result = stristr($result, "wx.cgiData = ");
	$result = substr($result, strlen("wx.cgiData = "), -1);
	$result = stristr($result, "{\"msg_item\":");
	$result = substr($result, strlen("{\"msg_item\":"), -1);
	$endpos = stripos($result, "}]}}");
	$result = substr($result, 0, $endpos+2);
	runlog ("$result\n");
	$arr = json_decode($result, 1);
	foreach ($arr as $subarr)
	{
		$c = $subarr["content"];
		$t = $subarr["date_time"];
		runlog ("$c $t\n");
		if (strcmp($subarr["content"], $content))
			continue;
		if (abs(intval($subarr["date_time"]) - $time) > 2)
			continue;
		return true;
	}
	return false;
}

function get_fid_by_msg(&$rfid, $username, $passwd, $content, $time, $dblink, $bizname)
{
	global $ROOTDIR;
	$userlist = $ROOTDIR."/getlist/userlist";
	$pexe = "phantomjs";
	$listjs = $ROOTDIR."/getlist/weixin_userlist.js";
	$fidjs = $ROOTDIR."/getlist/weixin_fid.js";

	$fp = popen("$pexe $listjs $username $passwd > $userlist", "r");
	if ($fp)
		pclose($fp);

	runlog("$pexe $listjs $username $passwd > $userlist");
	runlog(__FILE__.":".__LINE__);
	$f = 0;
	$handle = @fopen($userlist, "r");
	if ($handle) {
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			$fid = substr($buffer, 0, -1);
			if (is_numeric ($fid) === false)
				continue;
			if (strlen($fid) < 3)
				continue;
			if (is_exist_fakeid($dblink, $fid, $bizname) >= 1 )
				continue;
			$dstfile = "/tmp/".$fid.$bizname.".wx";
			runlog("popen $pexe $fidjs $username $passwd $fid > $dstfile");
			$fp = popen("$pexe $fidjs $username $passwd $fid > $dstfile", "r");
			if ($fp)
				pclose($fp);
			if(is_match($content, $time, $dstfile) === true)
			{
				$f = 1;
				unlink($dstfile);
				$rfid = $fid;
				break;
			}
			unlink($dstfile);
		}
		fclose($handle);
	}
	if ($f === 1)
		return true;
	return false;
}

function refresh_fid_biz($bizname, $wx_username, $username, $passwd, $dblink)
{
	global $ROOTDIR;
	$userlist = $ROOTDIR."/getlist/userlist";
	$pexe = "phantomjs";
	$listjs = $ROOTDIR."/getlist/weixin_userlist.js";

	$fp = popen("$pexe $listjs $username $passwd > $userlist", "r");
	if ($fp)
		pclose($fp);

	$handle = @fopen($userlist, "r");
	if ($handle) {
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			$fid = substr($buffer, 0, -1);
			if (is_numeric ($fid) === false)
				continue;
			if (strlen($fid) < 3)
				continue;
			if (is_exist_fakeid($dblink, $fid, $bizname) >= 0 )
				continue;
			insert_replace_fid_wx_username($bizname, $fid, $wx_username, $dblink, '0');
			break;
		}
		fclose($handle);
	}
	return true;
}

function send_msg_by_fid($username, $passwd, $fid, $msg)
{
	global $ROOTDIR;
	$pexe = "phantomjs";
	$sendjs = $ROOTDIR."/getlist/weixin_send.js";

	$msg = $msg."\n".date('l dS \of F Y h:i:s A')."\n"."店家合作商务QQ:643969177";

	runlog("$pexe $sendjs $username $passwd $fid $msg");
	$fp = popen("$pexe $sendjs $username $passwd $fid '$msg'", "r");
	if ($fp)
		pclose($fp);
}

?>

