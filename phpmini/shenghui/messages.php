<?php
	error_reporting(0);
	session_start();
	if(!$_SESSION["userid"]) header("Location:index.php");
	require_once("log.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	include("common.php");
        
	 $tel = $_GET['tel'];
	 $sql = "select wx_username from t_wx_info";
	 $sql .= " where msisdn = '$tel';";
	 $res =mysql_query($sql);
	 if(!$res) {
		 echo mysql_error();
		 die("���ݿ�����뷵�����ԡ�");
	 }

	 $wxname = ""; 
	 while($rows=mysql_fetch_array($res)){
		 $wxname = $rows[0];
		 break;
	 }
	 mysql_free_result($res);
	$sql = "select id, msg , modtime from t_wx_voice where wx_username = '$wxname' and flag = 0 ";
	$res = mysql_query($sql);

	$s = array();
	while($row = mysql_fetch_array($res)) {
		$s[$row[0]] = $row[1]."|".$row[2];
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
<li class="leaf"><a href="admin.php" >�û�����</a></li>
<li class="leaf"><a href="www.php" >��Ѷ����</a></li>
<li class="leaf"><a href="logout.php">ע����¼</a></li>

</ul>
</div>
</div>
</td>
   <td id="main" >
   <div class="breadcrumb"></div><h2>�ֻ����� <?php echo $_GET['tel']?></h2>
</div><hr />
<!-- begin content -->
</br>
<li class="leaf"><a href="register.php" >�����û�</a></li>
</br>
<form action="admin.php"  method="get" >
<div><div class="container-inline"><div class="form-item">
 <label >�����û�: </label>
�����ֻ�����<input type="text" name="name" /><br />
</div>
<input type="submit" value="��    ��"  class="form-submit" />
</div>
</div></form>

<table width="850">
 <thead><th>��������</th><th >����ʱ��</th><th>����</th> </tr></thead>
<tbody>
<?php
	foreach ($keys as $k)
	{
		$v = $s[$k];
		$r = parse_msg_com($v,"|" );
		 echo "<td width = \"0%\">{$r[0]}</td>";
		echo "<td>{$r[1]}</td>";
		echo "<td><a href='p_message.php?id={$k}&tel=$tel'>����</a> </td> </tr>";
	}
?>
</tbody></table>
<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
