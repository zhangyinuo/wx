<?php
	if($_POST["op"] == "更    新") {
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
<li class="leaf"><a href="account.php" class="active">我的帐户</a></li>
<li class="leaf"><a href="admin.php" >用户列表</a></li>
<li class="leaf"><a href="logout.php">注销登录</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">

<li><a href="account.php" class="active" >查看</a></li>
<li class="active"><a href="edit_account.php" class="active" >编辑</a></li>
</ul>
<script>
	function check_form() {
		return true;
	}
</script>
<!-- begin content -->
<form action="edit_account.php"  method="post" id="user_edit">

<div><div class="form-item">
 <label for="edit-name">用户名: </label>
<span><?php echo $username; ?></span>
</div>
<div class="form-item">
 <label for="edit-money">余额: </label>
 <input type="text" maxlength="6" name="edit[money]" id="edit-money"  size="30" value="" />
 <div class="description">用户余额:默认0.00</div>
</div>
<div class="form-item">
 <label for="edit-point">积分: </label>
 <input type="text" maxlength="32" name="edit[point]" id="edit-point"  size="30" value="" />
 <div class="description">用户积分</div>
</div>

<?php
if($_GET["id"]!="" && $_SESSION["userid"]==1) {
	echo "<input type='hidden' name='id' value='{$_GET['id']}' />";
}
?>
<input type="submit" name="op" value="更    新"  class="form-submit" onclick="return check_form();"  />
<br /><br />
</div></form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
