<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	session_start();
	require_once("log.php");
	date_default_timezone_set('Asia/Chongqing');
	$bizname = $_SESSION["username"];
	$form = check_form($_POST["edit"]);
	$msisdn = $form["tel"];
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
	$sql .= " '{$sex}',";
	$sql .= " '$m', ";
	$sql .= " '$p', ";
	$sql .= " '$curtime', ";
	$sql .= " '$curtime') ";
	 
	runlog($sql);
	
	$res = mysql_query($sql);
	if(!$res) {
		die("数据库出错，请返回重试。".":".mysql_error());
	}
	
	header("Location:msg.php?m=register_success");
?>
