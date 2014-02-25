<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	require_once("log.php");
	runlog($id.":",$m);
	
	header("Location:msg.php?m=update_success");
?>
