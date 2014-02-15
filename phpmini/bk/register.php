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
<script>
	function check_form() {
		return true;
	}
</script>
<!-- begin content -->
<form action="register.php"  method="post" id="user_register">

<div><div class="form-item">
 <label for="edit-tel">手机号码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="11" name="edit[tel]" id="edit-tel"  size="30" value="" class="form-text required" />
 <div class="description">客户手机号码</div>
</div>
<div class="form-item">
<label for="edit-sex">性别: </label>
保密 <input type="radio" name="edit[sex]"  value="0"  checked=checked />
男 <input type="radio" name="edit[sex]"   value="1" />
女 <input type="radio" name="edit[sex]"  value="2" />
</div>
<div class="form-item">
 <label for="edit-money">余额: </label>
 <input type="text" maxlength="8" name="edit[money]" id="edit-money"  size="30" value="" />
 <div class="description">用户余额</div>
</div>
<div class="form-item">
 <label for="edit-jifen">积分: </label>
 <input type="text" maxlength="64" name="edit[jifen]" id="edit-jifen"  size="30" value="" />
 <div class="description">用户积分</div>
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
