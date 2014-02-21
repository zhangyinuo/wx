<?php 

date_default_timezone_set('Asia/Chongqing');
function runlog($text){
	$logfile = "/data/app/applog/applog.log".date("Ymd");
	file_put_contents($logfile, date("D M j G:i:s T Y")." ".$text."\n",FILE_APPEND);		
};

function wx_log($text){
	$logfile = "/data/app/log/wxlog.log".date("Ymd");
	file_put_contents($logfile, date("D M j G:i:s T Y")." ".$text."\n",FILE_APPEND);		
};

?>
