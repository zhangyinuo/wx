<?php
	include("settings.inc.php");
	#connect to db
	$con = mysql_connect($dbhost,$dbuser,$dbpass);
	if(!$con) die("mysql error:".mysql_error());
	mysql_select_db($dbname,$con);
?>