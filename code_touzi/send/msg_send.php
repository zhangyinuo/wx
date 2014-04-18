<?php 
$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."db/db.php");
require_once($ROOTDIR."bizinfo/bizinfo.php");
require_once($ROOTDIR."queue/queue.php");
require_once($ROOTDIR."log/log.php");
require_once($ROOTDIR."token/token.php");

$dblink= get_db();
if ($dblink === false)
{
	echo "get db error!\n";
	exit;
}

init_bizinfo($dblink);

$token = "";
$type = 0;
while (1)
{
	while(msg_receive($wx_down_q, 0, $type, 40960, $message, TRUE, MSG_IPC_NOWAIT)) {

		runlog(__FILE__."_".__LINE__.":"."try send: ".$message);

		if (get_token_by_biz($token, "self_test", $dblink) === false)
		{
			echo "get_token_by_biz error!\n";
			return false;
		}

		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$token";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
		$response = curl_exec($ch);
		curl_close($ch);
	}
	mysql_ping($dblink);
	sleep(1);
}
?>
