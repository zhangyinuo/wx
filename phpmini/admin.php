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
<li class="leaf"><a href="account.php" class="active">我的帐户</a></li>
<li class="leaf"><a href="admin.php" >用户列表</a></li>
<li class="leaf"><a href="logout.php">注销登录</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
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
用户名模糊搜索：<input type="text" name="name"  value="<?php echo $_GET['name']; ?>" /><br />
</div>
<input type="submit" value="检    索"  class="form-submit" />
</div>
</div></form>

<table>
 <thead><tr><th> </th><th>用户名</th><th >性别</th><th>邮箱</th><th>注册时间</th><th>操作</th> </tr></thead>
<tbody>
<?php
	while($row = mysql_fetch_array($res)) {
		switch($row["sex"]) {
			case	"1"	:
				$sex = "男";
				break;
			case	"2"	:
				$sex = "女";
				break;
			default		:
				$sex = "保密";
				break;
		}
		echo "<tr ><td></td>";
		echo "<td>{$row['username']}</td>";
		echo "<td >{$sex}</td>";
		echo "<td>{$row['mail']}</td>";
		echo "<td class='active'>{$row['reg_time']}</td>";
		if($_SESSION["userid"]=="1") {
			echo "<td><a href='edit_account.php?id={$row['id']}'>编辑</a><br /><a  href='#' onclick='return doDel(\"{$row['username']}\",{$row['id']});'>删除</a> </td> </tr>";
		}else {
			echo "<td><a href='detail.php?id={$row['id']}'>查看</a></td> </tr>";
		}
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
