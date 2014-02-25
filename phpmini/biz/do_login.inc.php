<?php
	session_start();
	include("dbconnect.inc.php");
	require_once("log.php");
	include("functions.inc.php");
	$form = check_form($_POST["edit"]);
	$username = $form["name"];
	$password = md5($form["pass"]);
<<<<<<< HEAD
	$sql = "select  *  from biz_user where bizname='{$username}'  limit 1 ";
=======
	$sql = "select  *  from users where username='{$username}'  limit 1 ";
>>>>>>> e2a0f5043fd44a33281989a9a99da7e76d42d21a
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	if($password != $row["password"]) {
		header("Location:msg.php?m=login_error");
		runlog($username.":".$password."::".$row["password"]);
		exit;
	}
	
	$_SESSION["userid"] = $row["id"];
<<<<<<< HEAD
	$_SESSION["username"] = $row["cname"];
	$_SESSION["bizname"] = $username;;
	runlog($row["cname"]);
=======
	$_SESSION["username"] = $username;
>>>>>>> e2a0f5043fd44a33281989a9a99da7e76d42d21a
	header("Location:account.php");
?>
