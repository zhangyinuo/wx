<?php
	if($_POST["op"] == "��    ��") {
		include("do_photo.inc.php");
		exit;
	}
	include("header.inc.php");
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
 <div class="content">
<ul class="menu">

</ul>
</div>
</div>
</td>

<!-- begin content -->
<div class="profile"><h2 class="title">�ϴ���Ƭ</h2>
<form name="photo" method="post" action="photo.php" enctype="multipart/form-data" >
	�ϴ���Ƭ��
	<input type="file" name="photo" size="25" /><br /><br />
	<input type="submit" name="op" value="��    ��" />
</form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
