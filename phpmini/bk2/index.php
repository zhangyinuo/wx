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
 <h2 class="title">登录</h2>
 <div class="content"><form action="login.php"  method="post" id="user-login-form">
<div><div class="form-item">
 <label for="edit-name">姓名: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="60" name="edit[name]" id="edit-name"  size="15" value="" class="form-text required" />
</div>
<div class="form-item">
 <label for="edit-pass">密码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="" name="edit[pass]" id="edit-pass"  size="15"  class="form-text required" />
</div>
<input type="submit" name="op" value="登录"  class="form-submit" />
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
<li class="leaf"><a href="account.php" class="active">我的帐户</a></li>
<li class="leaf"><a href="admin.php" >用户列表</a></li>
<li class="leaf"><a href="logout.php">注销登录</a></li>

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
      <h1 class="title">欢迎使用微信后台系统</h1>
      <p>本系统主要功能有以下几点：</p>
      <ol>
        <li>
          <strong>会员注册</strong>
        </li>
        <li>
          <strong>会员信息修改</strong>
        </li>
        <li>
          <strong>会员信息查询</strong>
        </li>
        <li>
          <strong>管理员注册</strong>
        </li>
        <li>
          <strong>管理员登陆及管理</strong>
        </li>
      </ol>
<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
