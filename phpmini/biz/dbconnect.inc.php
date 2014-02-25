<?php
	include("settings.inc.php");
	#connect to db
	$con = mysql_connect($dbhost,$dbuser,$dbpass);
	if(!$con) die("mysql error:".mysql_error());
	mysql_select_db($dbname,$con);
<<<<<<< HEAD
	mysql_query("set names gb2312", $con);
=======
	mysql_query("set names utf8;", $con);
>>>>>>> e2a0f5043fd44a33281989a9a99da7e76d42d21a
?>
