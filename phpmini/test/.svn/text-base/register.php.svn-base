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

<input type="submit" name="op" value="注册新帐号"  class="form-submit" onclick="return check_form();"  />
<br /><br />
</div></form>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
