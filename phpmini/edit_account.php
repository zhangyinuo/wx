<?php
	if($_POST["op"] == "��    ��") {
		include("do_edit_account.inc.php");
		exit;
	}
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	if($_GET["id"]!="" && $_SESSION["userid"]==1 && is_numeric($_GET["id"])) {
		$id = $_GET["id"];
	}else {
		$id = $_SESSION["userid"];
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
<div class="breadcrumb"><a href="./">��ҳ</a> &raquo; <a href="./">�û��ʺ�</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">

<li><a href="account.php" class="active" >�鿴</a></li>
<li class="active"><a href="edit_account.php" class="active" >�༭</a></li>
</ul>
<script>
	function check_form() {
		password = document.getElementById("edit-pass").value;
		password2 = document.getElementById("edit-pass2").value;
		mail = document.getElementById("edit-mail").value;
		emsg = "";
		if(password != password2) emsg += "�����������벻ͬ. \n";
		var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
		if(!pattern.test(mail)) emsg += "�ʼ���ʽ����ȷ. \n";
		if(emsg != "" ) {
			emsg = "------------------------------------------\n\n"+emsg;
			emsg = emsg+"\n------------------------------------------";
			alert(emsg);
			return false;
		}else {
			return true;
		}
	}
</script>
<!-- begin content -->
<form action="edit_account.php"  method="post" id="user_edit">

<div><div class="form-item">
 <label for="edit-name">�û���: </label>
<span><?php echo $username; ?></span>
</div>
<div class="form-item">
 <label for="edit-pass">����(���޸�����������): </label>
 <input type="password" maxlength="64" name="edit[pass]" id="edit-pass"  size="30" value="" class="form-text required" />
 <div class="description">�������������롣</div>
 <input type="password" maxlength="64" name="edit[pass2]" id="edit-pass2"  size="30" value="" class="form-text required" />
 <div class="description">���ٴ������������롣</div>
</div>
<div class="form-item">
 <label for="edit-mail">E-mail��ַ: </label>
 <input type="text" maxlength="64" name="edit[mail]" id="edit-mail"  size="30" value="<?php echo $mail; ?>" class="form-text required" />
 <div class="description">�����ʼ���ַ����ȷ��������ȷ�ġ�</div>
</div>
<div class="form-item">
<label for="edit-sex">�Ա�: </label>
���� <input type="radio" name="edit[sex]"  value="0"  <?php if($sex==0) echo "checked=checked"; ?> />
�� <input type="radio" name="edit[sex]"   value="1"  <?php if($sex==1) echo "checked=checked"; ?> />
Ů <input type="radio" name="edit[sex]"  value="2"  <?php if($sex==2) echo "checked=checked"; ?> />
</div>
<div class="form-item">
 <label for="edit-tel">�绰: </label>
 <input type="text" maxlength="64" name="edit[tel]" id="edit-tel"  size="30" value="<?php echo $tel; ?>" />
 <div class="description">���ĵ绰����ȷ��������ȷ�ġ�</div>
</div>
<div class="form-item">
 <label for="edit-web">��վ: </label>
 <input type="text" maxlength="64" name="edit[web]" id="edit-web"  size="30" value="<?php echo $web; ?>" />
 <div class="description">������վ������еĻ���</div>
</div>
<div class="form-item">
 <label for="edit-birthday">����������: </label>
 <input type="text" maxlength="64" name="edit[birthday]" id="edit-birthday"  size="30" value="<?php echo $birthday; ?>" />
 <div class="description">����д���ĳ��������ա�</div>
</div>
<div class="form-item">
 <label for="edit-inter">����: </label>
 <textarea  name="edit[inter]" id="edit-inter"  rows="6" cols="30"><?php echo $inter; ?></textarea>
</div>
<div class="form-item">
 <label for="edit-intro">���ҽ���: </label>
 <textarea  name="edit[intro]" id="edit-intro"  rows="6" cols="30" ><?php echo $intro; ?></textarea>
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
