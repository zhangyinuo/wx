<?php
	session_start();
	include("dbconnect.inc.php");
	require_once("log.php");
	include("functions.inc.php");
	$form = check_form($_POST["edit"]);
	$username = $form["name"];
	$password = md5($form["pass"]);
	$sql = "select  *  from biz_user where bizname='{$username}'  limit 1 ";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	if($password != $row["password"]) {
		header("Location:msg.php?m=login_error");
		runlog($username.":".$password."::".$row["password"]);
		exit;
	}
	
	$_SESSION["userid"] = $row["id"];
	$_SESSION["username"] = $row["cname"];
	$_SESSION["bizname"] = $username;;
	runlog($row["cname"]);
	header("Location:account.php");
?>
