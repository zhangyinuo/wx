<?php
	session_start();
	if($_POST["op"] == "上    传") {
		include("do_photo.inc.php");
		exit;
	}
	include("header.inc.php");
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">我的帐户</a></li>
<?php 
	if($_SESSION["userid"]=="1") {
?>
<li class="leaf"><a href="admin.php" >管理</a></li>
<?php
	}
?>
<li class="leaf"><a href="logout.php">注销登录</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2>

<!-- begin content -->
<div class="profile"><h2 class="title">上传照片</h2>
<form name="photo" method="post" action="photo.php" enctype="multipart/form-data" >
	上传照片：
	<input type="file" name="photo" size="25" /><br /><br />
	<input type="submit" name="op" value="上    传" />
</form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
