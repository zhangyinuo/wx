<?php 
$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."bizinfo/bizinfo.php");

$prefix = "{\"access_token\":\"";
$midfix = "\",\"expires_in\":";
$suffix = "}";

function refresh_token_biz($bizname, $file, $dblink)
{
	global $ROOTDIR;
	$tmpfile = $file.posix_getpid();
	$lnfile = $file.".tmp";
	$pexe = $ROOTDIR."/token/phantomjs";
	$tokenjs = $ROOTDIR."/token/weixin_token.js";

	$id = "";
	$key = "";

	if (get_biz_info($bizname, $id, $key, $dblink) === false)
	{
		runlog(__FILE__."_".__LINE__.":"."get_biz_info:".$bizname);
		return false;
	}

	$fp = popen("$pexe $tokenjs $id $key > $tmpfile", "r");
	if ($fp)
		pclose($fp);

	$str = file_get_contents($tmpfile);

	$postObj = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);

	$s = $postObj->body->pre;
	$r = json_decode($s, true);
	$a = $r["access_token"];
	if (strlen($a) < 2)
	{
		runlog(__FILE__."_".__LINE__.":"."get token err:".$bizname);
		return false;
	}
	$t = $r["expires_in"] + time() - 300;

	file_put_contents($lnfile, $a." ".$t);
	return rename($lnfile, $file);
}

function get_token_by_biz(&$token, $bizname, $dblink)
{
	$curtime = time();

	$curdir = dirname(__FILE__)."/";
	$file = $curdir."token/".$bizname;
	$str = file_get_contents($file);
	list($token1, $ltime) = sscanf($str, "%s %s");
	if ($curtime >= $ltime)
	{
		if (refresh_token_biz($bizname, $file, $dblink))
		{
			$str = file_get_contents($file);
			list($token1, $ltime) = sscanf($str, "%s %s");
			$token = $token1;
			return true;
		}
		runlog(__FILE__.":".__LINE__."refresh_token_biz err");
		return false;
	}
	$token = $token1;
	return true;
}

?>

