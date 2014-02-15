<?php
	//error_reporting(0);
	if($_POST["op"] == "注册新帐号") {
		include("do_register.inc.php");
		exit;
	}
	include("header.inc.php");
?>
 <table id="content">
  <tr>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a></div><h2>用户帐号</h2><ul class="tabs primary">
<li class="active"><a href="register.php" class="active">注册</a></li>
<li><a href="login.php">登录</a></li>
</ul>
<script>
	function check_form() {
		username = document.getElementById("edit-name").value;
		password = document.getElementById("edit-pass").value;
		password2 = document.getElementById("edit-pass2").value;
		mail = document.getElementById("edit-mail").value;
		emsg = "";
		if(username == "") emsg += "用户名没有填写. \n";
		if(password == "") emsg += "密码没有填写. \n";
		if(password != password2) emsg += "两次输入密码不同. \n";
		var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
		if(!pattern.test(mail)) emsg += "邮件格式不正确. \n";
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
<form action="register.php"  method="post" id="user_register">

<div><div class="form-item">
 <label for="edit-name">用户名: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="60" name="edit[name]" id="edit-name"  size="30" value="" class="form-text required" />
 <div class="description">你的全名或你更喜欢的名字。允许中英文、空格和数字。</div>
</div>
<div class="form-item">
 <label for="edit-pass">密码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="64" name="edit[pass]" id="edit-pass"  size="30" value="" class="form-text required" />
 <div class="description">请输入您的密码。</div>
 <input type="password" maxlength="64" name="edit[pass2]" id="edit-pass2"  size="30" value="" class="form-text required" />
 <div class="description">请再次输入您的密码。</div>
</div>
<div class="form-item">
 <label for="edit-mail">E-mail地址: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="64" name="edit[mail]" id="edit-mail"  size="30" value="" class="form-text required" />
 <div class="description">您的邮件地址，请确保它是正确的。</div>
</div>
<div class="form-item">
<label for="edit-sex">性别: </label>
保密 <input type="radio" name="edit[sex]"  value="0"  checked=checked />
男 <input type="radio" name="edit[sex]"   value="1" />
女 <input type="radio" name="edit[sex]"  value="2" />
</div>
<div class="form-item">
 <label for="edit-tel">电话: </label>
 <input type="text" maxlength="64" name="edit[tel]" id="edit-tel"  size="30" value="" />
 <div class="description">您的电话，请确保它是正确的。</div>
</div>
<div class="form-item">
 <label for="edit-web">网站: </label>
 <input type="text" maxlength="64" name="edit[web]" id="edit-web"  size="30" value="" />
 <div class="description">您的网站，如果有的话。</div>
</div>
<div class="form-item">
 <label for="edit-birthday">出生年月日: </label>
 <input type="text" maxlength="64" name="edit[birthday]" id="edit-birthday"  size="30" value="" />
 <div class="description">请填写您的出生年月日。</div>
</div>
<div class="form-item">
 <label for="edit-inter">爱好: </label>
 <textarea  name="edit[inter]" id="edit-inter"  rows="6" cols="30"></textarea>
</div>
<div class="form-item">
 <label for="edit-intro">自我介绍: </label>
 <textarea  name="edit[intro]" id="edit-intro"  rows="6" cols="30" ></textarea>
</div>

<input type="submit" name="op" value="注册新帐号"  class="form-submit" onclick="return check_form();"  />
<br /><br />
</div></form>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
