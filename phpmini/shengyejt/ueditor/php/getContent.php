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

	$type = $_GET['type'];
	$id = $_GET['id'];

	$root = "/data/app/wx/phpmini/ueditor/php/html_shengyejt/";
	$path = $root.$type;
	if (file_exists($path) === false)
	{
		if (mkdir($path, 0755, true) === false)
			return;
	}

	$fname = $path."/".$id.".html";
	file_put_contents($fname, $_POST['myValue']);
	$pos = strpos($_POST['myValue'], "img src=\"");
	if ($pos === false)
		return;

	$epos = strpos($_POST['myValue'], "\"", $pos + 12);
	if ($epos === false)
		return;

	$imgurl = substr($_POST['myValue'], $pos + 9, $epos - $pos - 9);
	$imgname = $path."/".$id.".img";
	file_put_contents($imgname, $imgurl);

	$epos = strpos($_POST['myValue'], "</h1>");
	if ($epos === false)
		return;

	$pos = strpos($_POST['myValue'], "\">");
	if ($pos === false)
		return;

	$title = substr($_POST['myValue'], $pos + 2, $epos - $pos - 2);
	$tname = $path."/".$id.".title";
	file_put_contents($tname, $title);

?>
