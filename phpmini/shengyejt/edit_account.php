<?php
session_start();
include("dbconnect.inc.php");
include("functions.inc.php");
require_once("log.php");
if($_POST["op"] == "��    ��") {
		$tel = $_POST['tel'];
		$sadmin = $_POST['sadmin'];
		$role = $_POST['role'];

		$sql = "update t_wx_info set sadmin = '$sadmin', role = '$role'";

		$sql .= " where msisdn = $tel;";
		runlog($sql);
		$res = mysql_query($sql);
		if(!$res) {
			echo mysql_error();
			die("���ݿ�����뷵�����ԡ�");
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
<!-- begin content -->
<form action="edit_account.php"  method="post" id="user_edit">

<div><div class="form-item">
 <label for="edit-name">�û���: </label>
<span><?php echo $_GET['tel']; ?></span>
</div>
<?php
 echo "<input type='hidden' name='tel' value='{$_GET['tel']}' />";
?>

	<div class="form-item">
	<label for="edit-sadmin">˵��: </label>
	<input type="text" maxlength="256" name="sadmin" id="edit-sadmin"  size="256" />
	</div>

<br/>
<br/>
	<div class="form-item">
	<label for="edit-role">��ɫ: </label>
	��ͨ�ͻ�<input type="radio" name="role"  value="0"  checked=checked />
	ҵ��Ա <input type="radio" name="role"   value="1" />
	����Ա <input type="radio" name="role"  value="2" />
	</div>

<br/>
<br/>

<input type="submit" name="op" value="��    ��"  class="form-submit" onclick=""  />
<br /><br />
</div></form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
