<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	$id = $_GET["id"];
	$sql = "delete from t_wx_info where msisdn ={$id}";
	$res = mysql_query($sql);
	if(!$res) {
		echo mysql_error();
		die("���ݿ�����뷵�����ԡ�");
	}
	
	header("Location:msg.php?m=del_success");
?>
