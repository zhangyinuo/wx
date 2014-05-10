



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<script type="text/javascript">
    function returnweixin(){
		        window.location.href="http://www.gzsensoft.com/shengyejt/www.php";
				    
				    }
	</script>

</head>

<body>
<form id="myForm" action="/shengyejt/refresh_2_wx.php?type=<?php echo $_GET['type'] ?>&id=<?php echo $_GET['id'] ?>" method="post">
                <script type="text/plain" id="myEditor">这里的内容会推送给用户,请对自己编辑的内容负责</script>
                <input type="button" value="返回资源管理" onclick="returnweixin()"/>
        </form>


</body>
</html>
<?php
$keyp = "KEY1";
$indir = "/data/app/wx/phpmini/ueditor/php/html/shengyejt/";
$outdir = "/data/app/wx/code_shengyejt/file/file/";

$idx = 1;
$sub = 1;

for ( ; $idx < 4; $idx++)
{
	$outfile = $outdir.$keyp.$idx."/okmsg";
	$valid = 0;
	$subc = "";
	$c = "\"articles\": [\n";
	for ($sub = 1; $sub < 5; $sub++)
	{
		$htmlfile = $indir.$keyp.$idx."/$sub.html";
		$titlefile = $indir.$keyp.$idx."/$sub.title";
		$imgfile = $indir.$keyp.$idx."/$sub.img";

		if (is_file($htmlfile) == false || is_file($titlefile) == false || is_file($imgfile) == false )
			continue;

		$title = file_get_contents($titlefile);
		$title = iconv('GB2312', 'UTF-8', $title);
		$img = file_get_contents($imgfile);

		$url = "http://www.gzsensoft.com/wwz/wwz_shengyejt.php?type=$keyp$idx&id=$sub";

		$valid++;
		if ($valid > 1)
			$subc = $subc.",";

		$subc = $subc."{\n\"title\":\"$title\",\n\"description\":\"$title\",\n\"url\":\"$url\",\n\"picurl\":\"$img\"\n}\n";
	}
	if ($valid >= 1)
	{
		$c = $c.$subc."]\n";
		file_put_contents($outfile, $c);
	}
}

?>
