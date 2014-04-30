<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>历史记录</title>
</head>

<body>

<h1>历史记录</h1>

<?php
    error_reporting(0);
	session_start();
	if(!$_SESSION["userid"]) header("Location:index.php");
	require_once("log.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	include("common.php");

	
	$sql = "select id, msg,  modtime from t_wx_voice where  flag =1 order by msg desc limit 5";
 	$res= mysql_query($sql);
	$s = array();
	while($row = mysql_fetch_array($res)) 
	{
		$s[$row[0]] = $row[1]."|".$row[2]."|".$row[3];
	}
		$keys = array_keys($s);
		mysql_free_result($res);
?>



<table width="850">
 <thead>
<h1>历史记录</h1>
<th>内容</th><th></th><th></th><th>时间</th><th></th><th></th>
</thead>
<tbody>
<?php
	    foreach ($keys as $k)
		{
			$v = $s[$k];
			$r = parse_msg_com($v,"|" );
			echo "<td width = \"0%\">{$r[0]}</td>";
			//echo "<td>{$r[1]}</td>";
			//echo "<td><a href='p_message.php?id={$k}&tel=$tel'>处理</a> </td> </tr>";

		 }
?>
</tbody></table>









</body>
</html>






















