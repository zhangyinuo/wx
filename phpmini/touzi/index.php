<?php
	error_reporting(0);
	include("header.inc.php");
	session_start();
?>
 <table id="content">
  <tr>
<?php
	if(!$_SESSION["userid"]) {
?>
   <td id="sidebar-left"><div class="block block-user" id="block-user-0">
 <h2 class="title">��¼</h2>
 <div class="content"><form action="login.php"  method="post" id="user-login-form">
<div><div class="form-item">
 <label for="edit-name">����: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="60" name="edit[name]" id="edit-name"  size="15" value="" class="form-text required" />
</div>
<div class="form-item">
 <label for="edit-pass">����: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="" name="edit[pass]" id="edit-pass"  size="15"  class="form-text required" />
</div>
<input type="submit" name="op" value="��¼"  class="form-submit" />
</div></form>
</div>
</div>
</td>
<?php
	}else{
?>
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
<?php 
	}
?>
	
   <td id="main">

<!-- begin content -->
<div id="first-time">
      <h1 class="title">��ӭʹ��΢�ź�̨ϵͳ</h1>
      <p>��ϵͳ��Ҫ���������¼��㣺</p>
      <ol>
        <li>
          <strong>��Աע��</strong>
        </li>
        <li>
          <strong>��Ա��Ϣ�޸�</strong>
        </li>
        <li>
          <strong>��Ա��Ϣ��ѯ</strong>
        </li>
        <li>
          <strong>����Աע��</strong>
        </li>
        <li>
          <strong>����Ա��½������</strong>
        </li>
      </ol>
<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
