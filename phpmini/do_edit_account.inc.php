<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	$form = check_form($_POST["edit"]);
	extract($form);
	if($_POST["id"]!="" && $_SESSION["userid"]==1&& is_numeric($_POST["id"])) {
		$id = $_POST["id"];
	}else {
		$id = $_SESSION["userid"];
	}
	$sql = "update  users set ";
	#����{}�����Ǵ������ַ��������õ�ǰ�����ı���
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
		die("���ݿ�����뷵�����ԡ�");
	}
	
	header("Location:msg.php?m=update_success");
?>
