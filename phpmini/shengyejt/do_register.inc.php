<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	session_start();
	require_once("log.php");
	date_default_timezone_set('Asia/Chongqing');
	$bizname = $_SESSION["username"];
	$form = check_form($_POST["edit"]);
	$msisdn = $form["tel"];
	$wx_username = "wx_".$msisdn;
	$curtime = date("YmdHis");
	 
	$result = mysql_query("select count(1) from t_wx_info where msisdn = '$msisdn' ");
	if ($result === false)
	{
		runlog("query wx_username from t_wx_info is err:".$msisdn);
		die("�ֻ�����".":$msisdn ��ӳ���");
	}
	while($row=mysql_fetch_array($result)) 
	{
		if ($row[0] > 0)
			die("�ֻ�����".":$msisdn �Ѿ����ڣ�����ɾ�������");
		else
			break;
	}
	mysql_free_result($result);

	$sql = "insert into t_wx_info values(NULL, '$wx_username', '$wx_username', '$curtime', 'NULL', NULL, NULL, 'NULL', NULL, NULL, 0, 0, '$msisdn', 1, NULL, NULL, '$curtime', NULL, NULL, 0, 0);";
	$res = mysql_query($sql);
	if(!$res) {
		die("���ݿ�����뷵�����ԡ�".":".mysql_error());
	}
	
	header("Location:msg.php?m=register_success");
?>
