<?php 

$ROOTDIR=dirname(__FILE__)."/../";
require_once($ROOTDIR."token/token.php");


function update_menu($bizname, $dblink)
{
	global $ROOTDIR;
	$menu = $ROOTDIR."/file/menu/$bizname";
	$str = file_get_contents($menu);
	if (strlen($str) < 10)
	{
		runlog(__FILE__."_".__LINE__.":"."file_get_contents err: ".$menu);
		return false;
	}

	$token = "";
	if (get_token_by_biz($token, $bizname, $dblink) === false)
	{
		runlog(__FILE__."_".__LINE__.":"."get_token_by_biz err: ".$bizname);
		return false;
	}

	$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$token";

	$ret = http_post_data($url, $str);
	runlog(__FILE__."_".__LINE__.":".$ret);
}

?>

