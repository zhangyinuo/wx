<?php
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	if( is_numeric($_GET["id"])) {
		$id = $_GET["id"];
	}else {
		die("参数出错");
	}
	$sql = "select * from users where id={$id}";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	extract($row);
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
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="admin.php">用户列表</a></div>
<!-- begin content -->
<form action="edit_account.php"  method="post" id="user_edit">

<div><div class="form-item">
 <label for="edit-name">用户名: </label>
<span><?php echo $username; ?></span>
</div>
<div class="form-item">
 <label for="edit-mail">E-mail地址: </label>
<?php echo $mail; ?>
</div>
<div class="form-item">
<label for="edit-sex">性别: </label>
<?php if($sex==0) echo "保密"; ?>
<?php if($sex==1) echo "男"; ?>
<?php if($sex==2) echo "女"; ?>
</div>
<div class="form-item">
 <label for="edit-tel">电话: </label>
<?php echo $tel; ?>
</div>
<div class="form-item">
 <label for="edit-web">网站: </label>
<?php echo $web; ?>
</div>
<div class="form-item">
 <label for="edit-birthday">出生年月日: </label>
<?php echo $birthday; ?>
</div>
<div class="form-item">
 <label for="edit-inter">爱好: </label>
<pre>
<?php echo $inter; ?>
</pre>
</div>
<div class="form-item">
 <label for="edit-intro">自我介绍: </label>
<pre><?php echo $intro; ?></pre>
</div>
<a href="admin.php" >返回</a>
<br /><br />
</div></form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
