



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<script type="text/javascript">
    function returnweixin(){
		        window.location.href="http://www.gzsensoft.com/wisdom/www.php";
				    
				    }
	</script>

</head>

<body>
<form id="myForm" action="/wisdom/refresh_2_wx.php?type=<?php echo $_GET['type'] ?>&id=<?php echo $_GET['id'] ?>" method="post">
                <script type="text/plain" id="myEditor">这里的内容会推送给用户,请对自己编辑的内容负责</script>
                <input type="button" value="返回资源管理" onclick="returnweixin()"/>
        </form>


</body>
</html>
<?php
$keyp = "KEY";
$indir = "/data/app/wx/phpmini/ueditor/php/html/wisdom/";
$outdir = "/data/app/wx/phpmini/wwz/wisdom/";

$idx = 1;
$sub = 1;

for ( ; $idx < 4; $idx++)
{
	for ($sub = 1; $sub <= 5; $sub++)
	{
		$infile = $indir.$keyp.$idx."/$sub.html";
		$outfile = $outdir.$keyp.$idx."/$sub.html";

		if (file_exists($outfile))
			unlink($outfile);
		link($infile, $outfile);
	}
}

?>
