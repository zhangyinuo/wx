<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	require_once("log.php");

	$sql = {$tel};
	#���php�����У�magic_quotes_gpcû�б����ã���ִ�й����ַ�����
	$form = check_form($_POST["edit"]);
	date_default_timezone_set('Asia/Chongqing');
	extract($form);
	$sql = "insert into tel_user values ";
	#����{}�����Ǵ������ַ��������õ�ǰ�����ı���
	$sql .= " values('{$name}',";
	$sql .= " '{$pass}',";
	$sql .= " '{$sex}',";
	$sql .= " '{$mail}', ";
	$sql .= " '{$tel}', ";
	$sql .= " '{$web}', ";
	$sql .= " '{$birthday}', ";
	$sql .= " '{$inter}', ";
	$sql .= " '{$intro}', ";
	$sql .= " '{$reg_time}') ";
	
	$res = mysql_query($sql);
	if(!$res) {
		die("���ݿ�����뷵�����ԡ�".":".mysql_error());
	}
	
	header("Location:msg.php?m=register_success");
?>
