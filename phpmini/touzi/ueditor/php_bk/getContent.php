<meta http-equiv="Content-Type" content="text/html;charset=gbk"/>
<script src="../ueditor.parse.js" type="text/javascript"></script>
<script>
    uParse('.content',{
        'rootPath': '../'
    })

</script>
<?php
	include("../../dbconnect.inc.php");
    //获取数据
    error_reporting(E_ERROR|E_WARNING);
    $content =  htmlspecialchars(stripslashes($_POST['editor']));

	var_dump($_POST);

    //存入数据库或者其他操作

    //显示
    echo "第1个编辑器的值";
	echo  "<div class='content'>".htmlspecialchars_decode($content)."</div>";

?>
