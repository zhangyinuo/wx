<?php
	if($_POST["op"] == "��    ��") {
		include("do_edit_account.inc.php");
		exit;
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

<li><a href="account.php" class="active" >�鿴</a></li>
<li class="active"><a href="edit_account.php" class="active" >�༭</a></li>
</ul>
<script>
	function check_form() {
		return true;
	}
</script>
<!-- begin content -->
<form action="edit_account.php"  method="post" id="user_edit">

<div><div class="form-item">
 <label for="edit-name">�û���: </label>
<span><?php echo $username; ?></span>
</div>
<div class="form-item">
 <label for="edit-money">���: </label>
 <input type="text" maxlength="6" name="edit[money]" id="edit-money"  size="30" value="" />
 <div class="description">�û����:Ĭ��0.00</div>
</div>
<div class="form-item">
 <label for="edit-point">����: </label>
 <input type="text" maxlength="32" name="edit[point]" id="edit-point"  size="30" value="" />
 <div class="description">�û�����</div>
</div>

<?php
if($_GET["id"]!="" && $_SESSION["userid"]==1) {
	echo "<input type='hidden' name='id' value='{$_GET['id']}' />";
}
?>
<input type="submit" name="op" value="��    ��"  class="form-submit" onclick="return check_form();"  />
<br /><br />
</div></form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
