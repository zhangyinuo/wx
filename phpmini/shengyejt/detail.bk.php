<?php
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	if( is_numeric($_GET["id"])) {
		$id = $_GET["id"];
	}else {
		die("��������");
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
<li class="leaf"><a href="account.php" class="active">�ҵ��ʻ�</a></li>
<li class="leaf"><a href="admin.php" >�û��б�</a></li>
<li class="leaf"><a href="logout.php">ע����¼</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">��ҳ</a> &raquo; <a href="admin.php">�û��б�</a></div>
<!-- begin content -->
<form action="edit_account.php"  method="post" id="user_edit">

<div><div class="form-item">
 <label for="edit-name">�û���: </label>
<span><?php echo $username; ?></span>
</div>
<div class="form-item">
 <label for="edit-mail">E-mail��ַ: </label>
<?php echo $mail; ?>
</div>
<div class="form-item">
<label for="edit-sex">�Ա�: </label>
<?php if($sex==0) echo "����"; ?>
<?php if($sex==1) echo "��"; ?>
<?php if($sex==2) echo "Ů"; ?>
</div>
<div class="form-item">
 <label for="edit-tel">�绰: </label>
<?php echo $tel; ?>
</div>
<div class="form-item">
 <label for="edit-web">��վ: </label>
<?php echo $web; ?>
</div>
<div class="form-item">
 <label for="edit-birthday">����������: </label>
<?php echo $birthday; ?>
</div>
<div class="form-item">
 <label for="edit-inter">����: </label>
<pre>
<?php echo $inter; ?>
</pre>
</div>
<div class="form-item">
 <label for="edit-intro">���ҽ���: </label>
<pre><?php echo $intro; ?></pre>
</div>
<a href="admin.php" >����</a>
<br /><br />
</div></form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
