<?php
	session_start();
	if($_POST["op"] == "��    ��") {
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
<li class="leaf"><a href="account.php" class="active">�ҵ��ʻ�</a></li>
<?php 
	if($_SESSION["userid"]=="1") {
?>
<li class="leaf"><a href="admin.php" >����</a></li>
<?php
	}
?>
<li class="leaf"><a href="logout.php">ע����¼</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">��ҳ</a> &raquo; <a href="./">�û��ʺ�</a></div><h2><?php echo $_SESSION["username"]; ?></h2>

<!-- begin content -->
<div class="profile"><h2 class="title">�ϴ���Ƭ</h2>
<form name="photo" method="post" action="photo.php" enctype="multipart/form-data" >
	�ϴ���Ƭ��
	<input type="file" name="photo" size="25" /><br /><br />
	<input type="submit" name="op" value="��    ��" />
</form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
