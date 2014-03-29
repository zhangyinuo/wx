<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	if($_GET["id"]!="" && $_SESSION["userid"]==1 && is_numeric($_GET["id"]) && $_GET["id"]!="1") {
		$id = $_GET["id"];
	}else {
		die("您无权访问该页，请返回或重试。");
	}
	$sql = "delete from users where id={$id}";
	$res = mysql_query($sql);
	if(!$res) {
		echo mysql_error();
		die("数据库出错，请返回重试。");
	}
	
	header("Location:msg.php?m=del_success");
?>