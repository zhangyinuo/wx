<?php
	error_reporting(0);
	if($_POST["op"] == "登录") {
		include("do_login.inc.php");
		exit;
	}
	include("header.inc.php");
?>
 <table id="content">
  <tr>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a></div><h2>用户帐号</h2><ul class="tabs primary">
<li><a href="register.php">注册</a></li>
<li class="active"><a href="login.php" class="active">登录</a></li>
</ul>

<!-- begin content -->
<form action="login.php"  method="post" id="user_login">
<div><div class="form-item">
 <label for="edit-name">用户名: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="60" name="edit[name]" id="edit-name"  size="30" value="" tabindex="1" class="form-text required" />
 <div class="description">输入你的 local 用户名</div>
</div>
<div class="form-item">
 <label for="edit-pass">密码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="" name="edit[pass]" id="edit-pass"  size="30"  tabindex="2" class="form-text required" />
 <div class="description">输入你的密码。</div>
</div>
<input type="submit" name="op" value="登录"  tabindex="3" class="form-submit" />

</div></form>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
