<?php
<<<<<<< HEAD
session_start();
//header("Location:index.php");
$bizname = $_SESSION["bizname"];
require_once("log.php");
include("header.inc.php");
include("dbconnect.inc.php");
$sql = "select * from wx_user where bizname = '$bizname' order by lreqtime limit 50";
runlog($sql.":".$_SESSION["userid"].":".$_SESSION["username"]);
$res = mysql_query($sql);
=======
	session_start();
	if(!$_SESSION["userid"]) header("Location:index.php");
	require_once("log.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	if($_GET["name"] != "") {
		$name = addslashes($_GET["name"]);
		$where .= " and tel  = '{$name}' ";
	}
	$sql = "select * from tel_user where 1 {$where} limit 20";
	runlog($sql.":".$_SESSION["userid"].":".$_SESSION["username"]);
	$res = mysql_query($sql);
>>>>>>> e2a0f5043fd44a33281989a9a99da7e76d42d21a
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">�ҵ��ʻ�</a></li>
<<<<<<< HEAD
<li class="leaf"><a href="admin.php" >��������</a></li>
=======
<li class="leaf"><a href="admin.php" >�û��б�</a></li>
>>>>>>> e2a0f5043fd44a33281989a9a99da7e76d42d21a
<li class="leaf"><a href="logout.php">ע����¼</a></li>

</ul>
</div>
</div>
</td>
   <td id="main" >
<div class="breadcrumb"><a href="./">��ҳ</a></div><h2>�û��б�</h2><div class="help"><p>��ӭ�����û��б������������20������������ע���Ա�б���</p>
</div><hr />
<!-- begin content -->
<script language="javascript">
	function doDel(title,id) {
		if(confirm('��ȷ��Ҫɾ���û���\n-------------------------\n'+title+'\n-------------------------'))
			location.href='del_account.php?id='+id;
	}
</script>
<<<<<<< HEAD
=======
<li class="leaf"><a href="register.php" >�����û�</a></li>
<form action="admin.php"  method="get" >
<div><div class="container-inline"><div class="form-item">
 <label >�����û�: </label>
�����ֻ�����<input type="text" name="name"  value="<?php echo $_GET['name']; ?>" /><br />
</div>
<input type="submit" value="��    ��"  class="form-submit" />
</div>
</div></form>
>>>>>>> e2a0f5043fd44a33281989a9a99da7e76d42d21a

<table width="850">
 <thead><tr><th> </th><th>�ֻ�����</th><th >���</th><th>����</th><th>ע��ʱ��</th><th>�޸�ʱ��</th><th>����</th> </tr></thead>
<tbody>
<?php
	while($row = mysql_fetch_array($res)) {
		echo "<tr ><td></td>";
		echo "<td width = \"20%\">{$row['tel']}</td>";
		echo "<td >{$row['money']}</td>";
		echo "<td>{$row['point']}</td>";
		echo "<td width = \"20%\">{$row['regtime']}</td>";
		echo "<td width = \"20%\">{$row['modtime']}</td>";
		echo "<td><a href='edit_account.php?id={$row['id']}&m=money&tel={$row['tel']}'>�޸����</a><a>  </a><a a href='edit_account.php?id={$row['id']}&m=point&tel={$row['tel']}'>�޸Ļ���</a>  <a></a> <a  href='#' onclick='return doDel(\"{$row['tel']}\",{$row['id']});'>ɾ���û�</a></td> </tr>";
	}
?>
</tbody></table>
<?php 
	if(mysql_num_rows($res)==0) echo "û�м�������ص��û�";
?>
<!-- end content -->
   </td>
  </tr>
 </table>
<?php echo $page_link; ?>
 </body>
</html>