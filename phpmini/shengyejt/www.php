<?php
	session_start();
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">�ҵ��ʻ�</a></li>
<li class="leaf"><a href="admin.php" >�û�����</a></li>
<li class="leaf"><a href="www.php" >��Ѷ����</a></li>
<li class="leaf"><a href="logout.php">ע����¼</a></li>

</ul>
</div>
</div>
</td>
   <td id="main" >
<div class="breadcrumb"><a href="admin.php">��ҳ</a></div><h2>��Ѷ����</h2><div class="help"><p>��ӭʹ����Ѷ������</p>
</div><hr />
</br>
<div class="leaf"><a href="introduction_pic.php" >���Ž���</a></div>
</br>

<div class="leaf"><a href="subsidiary.php">�ӹ�˾����</a></div>

</br>

<div class="leaf"><a href="money.php" >������Ѷ</a></div>



</br>
<!--
</br>

<div class="leaf"><a href="news.php" >ʱ����Ϣ</a></div>
</br>

</br>
<div class="leaf"><a href="pushmsg.php" >ÿ������</a></div>
</br>
-->
</br>
<div class="leaf"><a href="refresh_2_wx.php" >ˢ�����ݵ�΢��</a></div>
</br>

   </td>
  </tr>
 </table>
 </body>
</html>
