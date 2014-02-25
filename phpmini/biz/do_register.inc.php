<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	session_start();
	require_once("log.php");
	date_default_timezone_set('Asia/Chongqing');
	$bizname = $_SESSION["username"];
	$form = check_form($_POST["edit"]);
	$msisdn = $form["tel"];
	$sex = $form["sex"];
	$fakeid = "wx_".$msisdn;
	$curtime = date("YmdHis");
	$m = $form["money"];
	if (strlen($m) === 0)
		$m = "0.00";
	$p = $form["point"];
	if (strlen($p) === 0)
		$p = 0;
	$sql = "insert into tel_user ";
	$sql .= " values(NULL, '$bizname', '$fakeid', '$msisdn',";
	$sql .= " '$sex',";
	$sql .= " '$m', ";
	$sql .= " '$p', ";
	$sql .= " '$curtime', ";
	$sql .= " '$curtime') ";
	 
	runlog($sql);

	$check_sql = "select * from tel_user where bizname = '$bizname' and tel = '$msisdn';";

	$is_exist = false;
	$check_res = mysql_query($check_sql);
	while($check_row = mysql_fetch_array($check_res))
	{
		$is_exist = true;
		break;
	}	
	mysql_free_result($check_res);
	if ($is_exist)
	{
		die("手机号码".":$msisdn 已经存在，请先删除再添加");
	}

	$res = mysql_query($sql);
	if(!$res) {
		die("数据库出错，请返回重试。".":".mysql_error());
	}
	
	header("Location:msg.php?m=register_success");
?>
