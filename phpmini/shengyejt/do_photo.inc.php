<?php
	include("dbconnect.inc.php");
	function fileExtName ($fStr) {
		$retval = "";
		$pt = strrpos($fStr, ".");
		if ($pt) $retval = substr($fStr, $pt+1, strlen($fStr) - $pt);
		return ($retval);
	}
	
	$uploaddir = './upload/';
	$ext = fileExtName($_FILES['photo']['name']);
	$ext = strtolower($ext);
	if($ext!="jpg" && $ext!="gif") {
		die("�ļ����ͳ����뷵�����ԡ�");
	}
	$uploadfile = $uploaddir . $_SESSION["username"].".".$ext;
	if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)) {
		$id = $_SESSION["userid"];
		$sql = "update users set photo='{$uploadfile}' where id={$id} ";
		if(mysql_query($sql))  {
			header("Location:msg.php?m=upload_success");
		}else {
			die("���ݿ����������");
		}
	} else {
		die("�ϴ�ʧ�ܣ��뷵�����ԡ�");
	}
?>