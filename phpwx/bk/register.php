<?php
	//error_reporting(0);
	if($_POST["op"] == "ע�����ʺ�") {
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
 <label for="edit-tel">�ֻ�����: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="11" name="edit[tel]" id="edit-tel"  size="30" value="" class="form-text required" />
 <div class="description">�ͻ��ֻ�����</div>
</div>
<div class="form-item">
<label for="edit-sex">�Ա�: </label>
���� <input type="radio" name="edit[sex]"  value="0"  checked=checked />
�� <input type="radio" name="edit[sex]"   value="1" />
Ů <input type="radio" name="edit[sex]"  value="2" />
</div>
<div class="form-item">
 <label for="edit-money">���: </label>
 <input type="text" maxlength="8" name="edit[money]" id="edit-money"  size="30" value="" />
 <div class="description">�û����</div>
</div>
<div class="form-item">
 <label for="edit-jifen">����: </label>
 <input type="text" maxlength="64" name="edit[jifen]" id="edit-jifen"  size="30" value="" />
 <div class="description">�û�����</div>
</div>

<input type="submit" name="op" value="ע�����ʺ�"  class="form-submit" onclick="return check_form();"  />
<br /><br />
</div></form>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
