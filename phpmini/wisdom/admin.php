<?php
	session_start();
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">我的帐户</a></li>
<li class="leaf"><a href="admin.php" >用户管理</a></li>
<li class="leaf"><a href="www.php" >资讯管理</a></li>
<li class="leaf"><a href="logout.php">注销登录</a></li>

</ul>
</div>
</div>
</td>
   <td id="main" >
<div class="breadcrumb"><a href="admin.php">主页</a></div><h2>资讯管理</h2><div class="help"><p>欢迎使用资讯管理功能</p>
</div><hr />
</br>
<div class="leaf"><a href="introduction_pic.php" >威士顿教育</a></div>
</br>

</br>
<div class="leaf"><a href="lesson.php" >课程咨询</a></div>
</br>

</br>
<div class="leaf"><a href="interactive.php" >交流互动</a></div>
</br>

</br>
<div class="leaf"><a href="pushmsg.php" >每日推送</a></div>
</br>

</br>
<div class="leaf"><a href="refresh_2_wx.php" >刷新内容到微网站</a></div>
</br>

   </td>
  </tr>
 </table>
 </body>
</html>
