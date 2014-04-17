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
	function doDel(id) {
		if(confirm('你确定要删除用户？\n-------------------------\n'+id+'\n-------------------------'))
			location.href='del_account.php?id='+id;
	}
</script>
</br>
<li class="leaf"><a href="register.php" >新增用户</a></li>
</br>
<form action="admin.php"  method="get" >
<div><div class="container-inline"><div class="form-item">
 <label >检索用户: </label>
输入手机号码<input type="text" name="name" /><br />
</div>
<input type="submit" value="检    索"  class="form-submit" />
</div>
</div></form>

<table width="850">
 <thead><tr><th> </th><th>手机号码</th><th >关注时间</th><th>最新互动时间</th><th>是否已经关注</th><th>是否已经派发</th><th>操作</th> </tr></thead>
<tbody>
<?php
	foreach ($keys as $k)
	{
		$v = $s[$k];
		$r = parse_msg_com($v, "|");
		$d = "是";
		if (intval($r[3]) === 0)
			$d = "否";
		$g = "是";
		if (intval($r[1]) === 1)
			$g = "否";
		echo "<tr ><td></td>";
		echo "<td width = \"12%\">{$k}</td>";
		echo "<td width = \"15%\">{$r[0]}</td>";
		echo "<td width = \"15%\">{$r[2]}</td>";
		echo "<td width = \"15%\">{$g}</td>";
		echo "<td width = \"15%\">{$d}</td>";
		echo "<td><a href='edit_account.php?tel={$k}'>修改</a> <a href='dispatch.php?tel={$k}'>派发</a> <a href='detail.php?tel={$k}'>详细</a> <a  href='#' onclick='return doDel(\"{$k}\");'>删除</a><a href='send_messages.php?tel={$k}'>发送</a></td> </tr>";
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
