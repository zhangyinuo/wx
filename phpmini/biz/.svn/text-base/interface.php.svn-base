<?php
session_start();
include("dbconnect.inc.php");
include("functions.inc.php");
require_once("log.php");
if($_POST["op"] == "更    新") {
		$m = $_POST['m'];
		$id = $_POST['id'];
		$tel = $_POST['tel'];
		$point = $_POST['point'];
		$money = $_POST['money'];
		runlog($_SESSION["username"].":".$id.":".$m.":".$tel.":".$point.":".$money);

		$sql = "update tel_user set ";
		if (strcmp($m, "money") === 0)
			if ($money > 0)
				$sql .= "money = money + $money ";
			else
			{
				$money = abs($money);
				$sql .= "money = money - $money ";
			}
		else
			if ($point > 0)
				$sql .= "point = point + $point";
			else
			{
				$point = abs($point);
				$sql .= "point = point - $point";
			}

		$sql .= " where id = $id;";
		runlog($sql);
		$res = mysql_query($sql);
		if(!$res) {
			echo mysql_error();
			die("数据库出错，请返回重试。");
		}

		header("Location:msg.php?m=update_success");
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
<!-- begin content -->
<form action="edit_account.php"  method="post" id="user_edit">

<div><div class="form-item">
 <label for="edit-name">用户名: </label>
<span><?php echo $_GET['tel']; ?></span>
</div>
<?php
 echo "<input type='hidden' name='id' value='{$_GET['id']}' />";
 echo "<input type='hidden' name='m' value='{$_GET['m']}' />";
 echo "<input type='hidden' name='tel' value='{$_GET['tel']}' />";
if (strcmp($_GET['m'], "money") === 0)
{
	echo "<div class=\"form-item\">\n";
	echo "<label for=\"edit-money\">余额: </label>\n";
	echo "<input type=\"text\" maxlength=\"6\" name=\"money\" id=\"edit-money\"  size=\"30\" value=\"\" />\n</div>\n";
}
else
{
	echo "<div class=\"form-item\">\n<label for=\"edit-point\">积分: </label>\n";
	echo "<input type=\"text\" maxlength=\"6\" name=\"point\" id=\"edit-point\"  size=\"30\" value=\"\" />\n</div>\n";
}
?>

<input type="submit" name="op" value="更    新"  class="form-submit" onclick=""  />
<br /><br />
</div></form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
