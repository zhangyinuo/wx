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

init_bizinfo($dblink);

$token = "";
if (get_token_by_biz($token, "self_test", $dblink) === false)
{
	echo "get_token_by_biz error!\n";
	return false;
}

echo "$token\n";

$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$token";

$data = file_get_contents("./msg");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);
curl_close($ch);
echo "$response";

?>

