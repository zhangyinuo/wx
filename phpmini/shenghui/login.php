<?php
	error_reporting(0);
	if($_POST["op"] == "��¼") {
		include("do_login.inc.php");
		exit;
	}
	include("header.inc.php");
?>
 <table id="content">
  <tr>
   <td id="main">
<div class="breadcrumb"><a href="./">��ҳ</a></div><h2>�û��ʺ�</h2><ul class="tabs primary">
<li><a href="register.php">ע��</a></li>
<li class="active"><a href="login.php" class="active">��¼</a></li>
</ul>

<!-- begin content -->
<form action="login.php"  method="post" id="user_login">
<div><div class="form-item">
 <label for="edit-name">�û���: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="60" name="edit[name]" id="edit-name"  size="30" value="" tabindex="1" class="form-text required" />
 <div class="description">������� local �û���</div>
</div>
<div class="form-item">
 <label for="edit-pass">����: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="" name="edit[pass]" id="edit-pass"  size="30"  tabindex="2" class="form-text required" />
 <div class="description">����������롣</div>
</div>
<input type="submit" name="op" value="��¼"  tabindex="3" class="form-submit" />

</div></form>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
