<?php
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
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">我的帐户</a></li>
<li class="leaf"><a href="admin.php" >用户列表</a></li>
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
输入手机号码<input type="text" name="name"  value="<?php echo $_GET['name']; ?>" /><br />
</div>
<input type="submit" value="检    索"  class="form-submit" />
</div>
</div></form>

<table width="750">
 <thead><tr><th> </th><th>手机号码</th><th >余额</th><th>积分</th><th>注册时间</th><th>修改时间</th><th>操作</th> </tr></thead>
<tbody>
<?php
	while($row = mysql_fetch_array($res)) {
		echo "<tr ><td></td>";
		echo "<td width = \"20%\">{$row['tel']}</td>";
		echo "<td >{$row['money']}</td>";
		echo "<td>{$row['point']}</td>";
		echo "<td width = \"20%\">{$row['regtime']}</td>";
		echo "<td width = \"20%\">{$row['modtime']}</td>";
		echo "<td><a href='edit_account.php?id={$row['id']}'>修改余额</a><a>  </a><a  href='#' onclick='return doDel(\"{$row['username']}\",{$row['id']});'>修改积分</a> </td> </tr>";
	}
?>
</tbody></table>
<?php 
	if(mysql_num_rows($res)==0) echo "没有检索到相关的用户";
?>
<!-- end content -->
   </td>
  </tr>
 </table>
<?php echo $page_link; ?>
 </body>
</html>
