<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#���php�����У�magic_quotes_gpcû�б����ã���ִ�й����ַ�����
	$form = check_form($_POST["edit"]);
	date_default_timezone_set('Asia/Chongqing');
	$form["reg_time"] = date("Y-m-d H:i:s");
	$form["pass"] = md5($form["pass"]);
	extract($form);
	$sql = "insert into users( username,password,sex,mail,tel,web,birthday,inter,intro,reg_time) ";
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
