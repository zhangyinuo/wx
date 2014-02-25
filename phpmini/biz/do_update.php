<?php
	include("header.inc.php");
	switch($_GET["m"]) {
		case	"register_success"	:
			$msg = "恭喜，帐号注册成功。<br />现在您可以使用您的用户名和密码登陆本系统了。<br />";
			$href = "<a href='index.php'>返回</a>";
			break;
		case	"update_success"	:
			$msg = "帐号信息更新成功。<br />";
			$href = "<a href='account.php'>返回</a>";
			break;
		case	"upload_success"	:
			$msg = "照片上传成功。<br />";
			$href = "<a href='account.php'>返回</a>";
			break;
		case	"del_success"	:
			$msg = "帐号信息删除成功，请返回。<br />";
			$href = "<a href='admin.php'>返回</a>";
			break;
		case	"mail_success"	:
			$msg = "修改密码确认邮件已经发送到您的信箱，请注意查收。<br />";
			$href = "<a href='index.php'>返回</a>";
			break;
		case	"login_error"	:
			$msg = "对不起，用户名或密码填写错误。<br />请返回重新填写。<br />";
			$href = "<a href='login.php'>返回</a>";
			break;
	}
?>
 <table id="content">
  <tr>
   <td id="main">
<div class="breadcrumb"><a href="/drupal/">主页</a></div><h2>消息</h2>
<!-- begin content -->

<?php echo $msg; ?>
<?php echo $href; ?>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
