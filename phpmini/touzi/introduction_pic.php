<?php
	session_start();
	if($_POST["op"] == "上    传") {
		include("do_introduction.inc.php");
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

<!-- begin content -->
<div class="profile"><h2 class="title">公司简介管理, 按顺序显示</h2>
<form name="pic0" method="post" action="introduction.php" enctype="multipart/form-data" >
	上传照片：
	<input type="file" name="pic0" size="25" /><br />
	<input type="submit" name="opic0" value="上    传" />
</form>
<br/>
<form name="html0" method="post" action="introduction.php" enctype="multipart/form-data" >
	上传详细文件(html):
	<input type="file" name="html0" size="25" /><br />
	<input type="submit" name="ohtml0" value="上    传" />
</form>

<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
