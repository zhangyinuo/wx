<?php
	session_start();
	if(!$_SESSION["userid"]) header("Location:index.php");
	require_once("log.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	if($_GET["name"] != "") {
		$name = addslashes($_GET["name"]);
		$where .= " and username like '%{$name}%' ";
	}
	$sql = "select * from users where 1 {$where} limit 20";
	runlog($sql.":".$_SESSION["userid"].":".$_SESSION["username"]);
	$res = mysql_query($sql);
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">�ҵ��ʻ�</a></li>
<li class="leaf"><a href="admin.php" >�û��б�</a></li>
<li class="leaf"><a href="logout.php">ע����¼</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">��ҳ</a></div><h2>�û��б�</h2><div class="help"><p>��ӭ�����û��б����������20������������ע���Ա�б�</p>
</div><hr />
<!-- begin content -->
<script language="javascript">
	function doDel(title,id) {
		if(confirm('��ȷ��Ҫɾ���û���\n-------------------------\n'+title+'\n-------------------------'))
			location.href='del_account.php?id='+id;
	}
</script>
<li class="leaf"><a href="register.php" >�����û�</a></li>
<form action="admin.php"  method="get" >
<div><div class="container-inline"><div class="form-item">
 <label >�����û�: </label>
�û���ģ��������<input type="text" name="name"  value="<?php echo $_GET['name']; ?>" /><br />
</div>
<input type="submit" value="��    ��"  class="form-submit" />
</div>
</div></form>

<table>
 <thead><tr><th> </th><th>�û���</th><th >�Ա�</th><th>����</th><th>ע��ʱ��</th><th>����</th> </tr></thead>
<tbody>
<?php
	while($row = mysql_fetch_array($res)) {
		switch($row["sex"]) {
			case	"1"	:
				$sex = "��";
				break;
			case	"2"	:
				$sex = "Ů";
				break;
			default		:
				$sex = "����";
				break;
		}
		echo "<tr ><td></td>";
		echo "<td>{$row['username']}</td>";
		echo "<td >{$sex}</td>";
		echo "<td>{$row['mail']}</td>";
		echo "<td class='active'>{$row['reg_time']}</td>";
		if($_SESSION["userid"]=="1") {
			echo "<td><a href='edit_account.php?id={$row['id']}'>�༭</a><br /><a  href='#' onclick='return doDel(\"{$row['username']}\",{$row['id']});'>ɾ��</a> </td> </tr>";
		}else {
			echo "<td><a href='detail.php?id={$row['id']}'>�鿴</a></td> </tr>";
		}
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
