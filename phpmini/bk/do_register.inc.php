<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	require_once("log.php");

	$sql = {$tel};
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	$form = check_form($_POST["edit"]);
	date_default_timezone_set('Asia/Chongqing');
	extract($form);
	$sql = "insert into tel_user values ";
	#这里{}符号是代表在字符串中引用当前环境的变量
	$sql .= " values('{$name}',";
	$sql .= " '{$pass}',";
	$sql .= " '{$sex}',";
	$sql .= " '{$mail}', ";
	$sql .= " '{$tel}', ";
	$sql .= " '{$web}', ";
	$sql .= " '{$birthday}', ";
	$sql .= " '{$inter}', ";
	$sql .= " '{$intro}', ";
	$sql .= " '{$reg_time}') ";
	
	$res = mysql_query($sql);
	if(!$res) {
		die("数据库出错，请返回重试。".":".mysql_error());
	}
	
	header("Location:msg.php?m=register_success");
?>
