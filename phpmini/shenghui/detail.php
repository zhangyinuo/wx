<?php
	session_start();
	if(!$_SESSION["userid"]) header("Location:index.php");
	require_once("log.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	include("common.php");
	$name = trim($_GET["tel"]);
	$sql = "select lastmsg from t_wx_last where wx_username = (select wx_username from t_wx_info where msisdn = '$name') ";
	$res = mysql_query($sql);
	$lastmsg = "";
	while($row = mysql_fetch_array($res)) {
		$lastmsg = $row[0];
		break;
	}
	mysql_free_result($res);
	$where = "";
	if($_GET["tel"] != "") {
		$name = trim($_GET["tel"]);
		$where .= " and msisdn = '{$name}' ";
	}
	$sql = "select msisdn, modtime, sadmin, atime from t_wx_info where 1 {$where} and flag = 0 limit 20";
	runlog($sql.":".$_SESSION["userid"].":".$_SESSION["username"]);
	$res = mysql_query($sql);

	$s = array();
	while($row = mysql_fetch_array($res)) {
		$s[$row[0]] = $row[1]."|".$row[2]."|".$row[3]."|".$lastmsg;
	}
	$keys = array_keys($s);
	mysql_free_result($res);
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">�ҵ��ʻ�</a></li>
<li class="leaf"><a href="admin.php" >�û�����</a></li>
<li class="leaf"><a href="www.php" >��Ѷ����</a></li>
<li class="leaf"><a href="logout.php">ע����¼</a></li>

</ul>
</div>
</div>
</td>
   <td id="main" >
<div class="breadcrumb"><a href="index.php">��ҳ</a></div><div class="help"><p>���û���ϸ��Ϣ</p>
</div><hr />
<!-- begin content -->

<table width="850">
 <thead><tr><th> </th><th>�ֻ�����</th><th >��עʱ��</th><th>���»���ʱ��</th><th>�ر�˵��</th><th>�û���ʷ��¼</th> </tr></thead>
<tbody>
<?php
	foreach ($keys as $k)
	{
		$v = $s[$k];
		$r = parse_msg_com($v, "|");
		echo "<tr ><td></td>";
		echo "<td width = \"20%\">{$k}</td>";
		echo "<td width = \"20%\">{$r[0]}</td>";
		echo "<td width = \"20%\">{$r[2]}</td>";
		echo "<td width = \"20%\">{$r[1]}</td>";
		echo "<td width = \"20%\">{$r[3]}</td>";
	}
?>
</tbody></table>
<?php 
	if(count($keys)==0) echo "û�м�������ص��û�";
?>
<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
