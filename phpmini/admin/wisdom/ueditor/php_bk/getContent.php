<meta http-equiv="Content-Type" content="text/html;charset=gbk"/>
<script src="../ueditor.parse.js" type="text/javascript"></script>
<script>
    uParse('.content',{
        'rootPath': '../'
    })

</script>
<?php
	include("../../dbconnect.inc.php");
    //��ȡ����
    error_reporting(E_ERROR|E_WARNING);
    $content =  htmlspecialchars(stripslashes($_POST['editor']));

	var_dump($_POST);

    //�������ݿ������������

    //��ʾ
    echo "��1���༭����ֵ";
	echo  "<div class='content'>".htmlspecialchars_decode($content)."</div>";

?>