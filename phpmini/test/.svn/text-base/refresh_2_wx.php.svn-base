<?php
$keyp = "KEY1";
$indir = "/data/app/wx/phpmini/ueditor/php/html/";
$outdir = "/data/app/wx/code_touzi/file/file/";

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

		$url = "http://203.195.190.177/wwz/wwz.php?type=$keyp$idx&id=$sub";

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
