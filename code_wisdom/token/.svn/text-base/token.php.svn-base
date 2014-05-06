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
	$lnfile = $file.".tmp";

	$id = "";
	$key = "";

	if (get_biz_info($bizname, $id, $key, $dblink) === false)
	{
		runlog(__FILE__."_".__LINE__.":"."get_biz_info:".$bizname);
		return false;
	}

	$cururl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$id&secret=$key";
	unlink("./tmptoken.txt");
	$fp = fopen("./tmptoken.txt", "w");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $cururl);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	$cret = curl_exec($ch);
	fclose($fp);
	curl_close($ch);
	$result = file_get_contents("./tmptoken.txt");
	$pos = strpos($result, "{\"access_token");
	if ($pos === false)
	{
		runlog(__FILE__."_".__LINE__.":"."http_get:".$bizname."::".$result);
		return false;
	}

	$str = substr($result, $pos);

	$r = json_decode($str, true);
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

