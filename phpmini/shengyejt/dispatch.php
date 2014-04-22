<?php
session_start();
include("dbconnect.inc.php");
include("functions.inc.php");
include("/data/app/wx/code_touzi/queue/queue.php");
require_once("log.php");
if($_POST["op"] == "派发任务") {
		$tel = $_POST['tel'];
		$yw_name = $_POST['yw_name'];
		$yw_msisdn = $_POST['yw_msisdn'];

		$ssql = "select wx_username from t_wx_info where msisdn = '$yw_msisdn' and flag = 0";
		$res = mysql_query($ssql);

		runlog($ssql);
		$dstwx = "";
		while($row = mysql_fetch_array($res)) {
			$dstwx = $row[0];
			break;
		}
		mysql_free_result($res);
		if ($dstwx == "")
		{
			die ("$yw_msisdn 并没有关注本微信公众号, 没法通过本系统派发任务");
		}

		$sql = "update t_wx_info set yw_name = '$yw_name', yw_msisdn = '$yw_msisdn', dispatch = 1 ";

		$sql .= " where msisdn = $tel;";
		runlog($sql);
		$res = mysql_query($sql);
		if(!$res) {
			echo mysql_error();
			die("数据库出错，请返回重试。");
		}

		header("Location:msg.php?m=dispatch_success");
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
<!-- begin content -->
<form action="dispatch.php"  method="post" id="user_edit">

<div><div class="form-item">
 <label for="edit-name">用户名: </label>
<span><?php echo $_GET['tel']; ?></span>
</div>
<?php
 echo "<input type='hidden' name='tel' value='{$_GET['tel']}' />";
?>

	<div class="form-item">
	<label for="edit-yw_name">业务员姓名: </label>
	<input type="text" maxlength="256" name="yw_name" id="edit-yw_name"  size="256" />
	</div>

<br/>
<br/>

	<div class="form-item">
	<label for="edit-yw_msisdn">业务员手机号码: </label>
	<input type="text" maxlength="256" name="yw_msisdn" id="edit-yw_msisdn"  size="256" />
	</div>

<br/>
<br/>

<input type="submit" name="op" value="派发任务"  class="form-submit" onclick=""  />
<br /><br />
</div></form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
