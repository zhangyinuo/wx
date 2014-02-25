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
		die("文件类型出错，请返回重试。");
	}
	$uploadfile = $uploaddir . $_SESSION["username"].".".$ext;
	if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)) {
		$id = $_SESSION["userid"];
		$sql = "update users set photo='{$uploadfile}' where id={$id} ";
		if(mysql_query($sql))  {
			header("Location:msg.php?m=upload_success");
		}else {
			die("数据库错误，请重试");
		}
	} else {
		die("上传失败，请返回重试。");
	}
?>