<?php
	session_start();
	if(!$_SESSION["userid"]) header("Location:index.php");
	require_once("log.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	include("common.php");
        
	$where = "";
	if($_GET["name"] != "") {
		$name = trim($_GET["name"]);
		$where .= " and msisdn = '{$name}' ";
	}
	$sql = "select msisdn, modtime, flag, atime, dispatch, role from t_wx_info where 1 {$where} order by dispatch limit 20";
	runlog($sql.":".$_SESSION["userid"].":".$_SESSION["username"]);
	$res = mysql_query($sql);

	$s = array();
	while($row = mysql_fetch_array($res)) {
		$s[$row[0]] = $row[1]."|".$row[2]."|".$row[3]."|".$row[4]."|".$row[5];
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
<div class="breadcrumb"><a href="./">��ҳ</a></div><h2>�û��б�</h2><div class="help"><p>��ӭ�����û��б����������20������������ע���Ա�б�</p>
</div><hr />
<!-- begin content -->
<script language="javascript">
	function doDel(id) {
		if(confirm('��ȷ��Ҫɾ���û���\n-------------------------\n'+id+'\n-------------------------'))
			location.href='del_account.php?id='+id;
	}
</script>
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
 <thead><tr><th> </th><th>�ֻ�����</th><th >��עʱ��</th><th>���»���ʱ��</th><th>�Ƿ��Ѿ���ע</th><th>�Ƿ��Ѿ��ɷ�</th><th>����</th> </tr></thead>
<tbody>
<?php
	foreach ($keys as $k)
	{
		$v = $s[$k];
		$r = parse_msg_com($v, "|");
		$d = "��";
		if (intval($r[3]) === 0)
			$d = "��";
		$g = "��";
		if (intval($r[1]) === 1)
			$g = "��";
		echo "<tr ><td></td>";
		echo "<td width = \"12%\">{$k}</td>";
		echo "<td width = \"15%\">{$r[0]}</td>";
		echo "<td width = \"15%\">{$r[2]}</td>";
		echo "<td width = \"15%\">{$g}</td>";
		echo "<td width = \"15%\">{$d}</td>";
		echo "<td><a href='edit_account.php?tel={$k}'>�޸�</a> <a href='dispatch.php?tel={$k}'>�ɷ�</a> <a href='detail.php?tel={$k}'>��ϸ</a> <a  href='#' onclick='return doDel(\"{$k}\");'>ɾ��</a><a href='send_messages.php?tel={$k}'>����</a></td> </tr>";
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
