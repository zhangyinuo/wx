<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."token/token.php");

$dblink = get_db();

if ($dblink === false)
{
	echo "get db error!\n";
	exit;
}

$token = "";
if (get_token_by_biz($token, "self_test", $dblink) === false)
{
	echo "get_token_by_biz error!\n";
	return false;
}

echo "$token\n";

$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$token";

$data = file_get_contents("./msg");

$ret = http_post_data($url, $data);

echo "$ret\n";

?>

