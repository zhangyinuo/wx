<?php
	include("header.inc.php");
	switch($_GET["m"]) {
		case	"register_success"	:
			$msg = "��ϲ���ʺ�ע��ɹ���<br />����������ʹ�������û����������½��ϵͳ�ˡ�<br />";
			$href = "<a href='index.php'>����</a>";
			break;
		case	"update_success"	:
			$msg = "�ʺ���Ϣ���³ɹ���<br />";
			$href = "<a href='account.php'>����</a>";
			break;
		case	"upload_success"	:
			$msg = "��Ƭ�ϴ��ɹ���<br />";
			$href = "<a href='account.php'>����</a>";
			break;
		case	"del_success"	:
			$msg = "�ʺ���Ϣɾ���ɹ����뷵�ء�<br />";
			$href = "<a href='admin.php'>����</a>";
			break;
		case	"mail_success"	:
			$msg = "�޸�����ȷ���ʼ��Ѿ����͵��������䣬��ע����ա�<br />";
			$href = "<a href='index.php'>����</a>";
			break;
		case	"login_error"	:
			$msg = "�Բ����û�����������д����<br />�뷵��������д��<br />";
			$href = "<a href='login.php'>����</a>";
			break;
	}
?>
 <table id="content">
  <tr>
   <td id="main">
<div class="breadcrumb"><a href="/drupal/">��ҳ</a></div><h2>��Ϣ</h2>
<!-- begin content -->

<?php echo $msg; ?>
<?php echo $href; ?>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
