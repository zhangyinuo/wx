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
<li class="leaf"><a href="account.php" class="active">�ҵ��ʻ�</a></li>
<li class="leaf"><a href="admin.php" >�û��б�</a></li>
<li class="leaf"><a href="logout.php">ע����¼</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">��ҳ</a> &raquo; <a href="./">�û��ʺ�</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">

<li class="active"><a href="account.php" class="active">�鿴</a></li>
</ul>

<!-- begin content -->
<div class="profile"><h2 class="title">���˵���</h2>
<dl><dt class="user-member">��Ƭ</dt><dd class="user-member"><a href="photo.php"><img src="<?php echo $photo; ?>" border="0" /></a></dd></dl>
<dl><dt class="user-member">ע��ʱ��</dt><dd class="user-member"><?php echo $reg_time; ?></dd></dl></div>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
