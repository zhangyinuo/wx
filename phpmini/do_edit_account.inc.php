<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	$form = check_form($_POST["edit"]);
	extract($form);
	if($_POST["id"]!="" && $_SESSION["userid"]==1&& is_numeric($_POST["id"])) {
		$id = $_POST["id"];
	}else {
		$id = $_SESSION["userid"];
	}
	$sql = "update  users set ";
	#这里{}符号是代表在字符串中引用当前环境的变量
	if($form["pass"] != "") {
		$form["pass"] = md5($form["pass"]);
		$sql .= "password='{$pass}', ";
	}	
	$sql .= " sex='{$sex}',";
	$sql .= " mail='{$mail}', ";
	$sql .= " tel='{$tel}', ";
	$sql .= " web='{$web}', ";
	$sql .= " birthday='{$birthday}', ";
	$sql .= " inter='{$inter}', ";
	$sql .= " intro='{$intro}' ";
	$sql .= " where id={$id} ";
	$res = mysql_query($sql);
	if(!$res) {
		echo mysql_error();
		die("数据库出错，请返回重试。");
	}
	
	header("Location:msg.php?m=update_success");
?>