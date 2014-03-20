<?php
	if($_POST["op"] == "上    传") {
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
<div class="profile"><h2 class="title">上传照片</h2>
<form name="photo" method="post" action="photo.php" enctype="multipart/form-data" >
	上传照片：
	<input type="file" name="photo" size="25" /><br /><br />
	<input type="submit" name="op" value="上    传" />
</form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
