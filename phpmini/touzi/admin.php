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
	$sql = "select msisdn, modtime, sadmin, atime from t_wx_info where 1 {$where} and flag = 0 limit 20";
	runlog($sql.":".$_SESSION["userid"].":".$_SESSION["username"]);
	$res = mysql_query($sql);

	$s = array();
	while($row = mysql_fetch_array($res)) {
		$s[$row[0]] = $row[1]."|".$row[2]."|".$row[3];
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
<li class="leaf"><a href="account.php" class="active">我的帐户</a></li>
<li class="leaf"><a href="admin.php" >用户管理</a></li>
<li class="leaf"><a href="www.php" >资讯管理</a></li>
<li class="leaf"><a href="logout.php">注销登录</a></li>

</ul>
</div>
</div>
</td>
   <td id="main" >
<div class="breadcrumb"><a href="./">主页</a></div><h2>用户列表</h2><div class="help"><p>欢迎来到用户列表。下面是最近20个符合条件的注册会员列表。</p>
</div><hr />
<!-- begin content -->
<script language="javascript">
	function doDel(title,id) {
		if(confirm('你确定要删除用户？\n-------------------------\n'+title+'\n-------------------------'))
			location.href='del_account.php?id='+id;
	}
</script>
<li class="leaf"><a href="register.php" >新增用户</a></li>
<form action="admin.php"  method="get" >
<div><div class="container-inline"><div class="form-item">
 <label >检索用户: </label>
输入手机号码<input type="text" name="name" /><br />
</div>
<input type="submit" value="检    索"  class="form-submit" />
</div>
</div></form>

<table width="850">
 <thead><tr><th> </th><th>手机号码</th><th >关注时间</th><th>最新互动时间</th><th>特别说明</th><th>操作</th> </tr></thead>
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
		echo "<td><a href='edit_account.php?id={$row['id']}&m=money&tel={$row['tel']}'>修改余额</a><a>  </a><a a href='edit_account.php?id={$row['id']}&m=point&tel={$row['tel']}'>修改积分</a>  <a></a> <a  href='#' onclick='return doDel(\"{$row['tel']}\",{$row['id']});'>删除用户</a></td> </tr>";
	}
?>
</tbody></table>
<?php 
	if(count($keys)==0) echo "没有检索到相关的用户";
?>
<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
