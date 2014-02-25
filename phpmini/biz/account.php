<?php
	session_start();
	if(!$_SESSION["userid"]) header("Location:index.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	$id = $_SESSION["userid"];
	$sql = "select * from users where id={$id}";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$reg_time = $row["reg_time"];
	$photo = $row["photo"];
	if($photo == "") {
		$photo = "logo.jpg";
	}
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
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">

<li class="active"><a href="account.php" class="active">查看</a></li>
</ul>

<!-- begin content -->
<div class="profile"><h2 class="title">个人档案</h2>
<dl><dt class="user-member">照片</dt><dd class="user-member"><a href="photo.php"><img src="<?php echo $photo; ?>" border="0" /></a></dd></dl>
<dl><dt class="user-member">注册时间</dt><dd class="user-member"><?php echo $reg_time; ?></dd></dl></div>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
