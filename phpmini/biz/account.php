<?php
	session_start();
	if(!$_SESSION["userid"]) header("Location:index.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">�ҵ��ʻ�</a></li>
<li class="leaf"><a href="interface.php" >��������</a></li>
<li class="leaf"><a href="logout.php">ע����¼</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">��ҳ</a> &raquo; <a href="./">�û��ʺ�</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">

<li class="active"><a href="account.php" class="active">�鿴</a></li>
</ul>

   </td>
  </tr>
 </table>

 </body>
</html>
