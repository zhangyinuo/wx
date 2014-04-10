<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	session_start();
	require_once("log.php");
	date_default_timezone_set('Asia/Chongqing');
	$bizname = $_SESSION["username"];
	$form = check_form($_POST["edit"]);
	$msisdn = $form["tel"];
	$wx_username = "wx_".$msisdn;
	$curtime = date("YmdHis");
	 
	$result = mysql_query("select count(1) from t_wx_info where msisdn = '$msisdn' ");
	if ($result === false)
	{
		runlog("query wx_username from t_wx_info is err:".$msisdn);
		die("手机号码".":$msisdn 添加出错");
	}
	while($row=mysql_fetch_array($result)) 
	{
		if ($row[0] > 0)
			die("手机号码".":$msisdn 已经存在，请先删除再添加");
		else
			break;
	}
	mysql_free_result($result);

	$sql = "insert into t_wx_info values(NULL, '$wx_username', '$wx_username', '$curtime', 'NULL', NULL, NULL, 'NULL', NULL, NULL, 0, 0, '$msisdn', 1, NULL, NULL, '$curtime', NULL, NULL, 0, 0);";
	$res = mysql_query($sql);
	if(!$res) {
		die("数据库出错，请返回重试。".":".mysql_error());
	}
	
	header("Location:msg.php?m=register_success");
?>
