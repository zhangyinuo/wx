<?php
	error_reporting(0);
	session_start();
	if(!$_SESSION["userid"]) header("Location:index.php");
	require_once("log.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	include("common.php");
        
	 $tel = $_GET['tel'];
	 $id = $_GET['id'];
	 $sql = "update t_wx_voice set flag = 1 where id = $id";
	 $res =mysql_query($sql);
	 if(!$res) {
		 echo mysql_error();
		 die("数据库出错，请返回重试。");
	 }
	header("Location:messages.php?tel=$tel")


?>
