<?php 

$ROOTDIR=dirname(__FILE__)."/../";
$curdir=dirname(__FILE__)."/";

require_once($ROOTDIR."log/log.php");

function get_content($bizname, $bfile)
{
	global $curdir;
	$path = $curdir.$bizname."/".$bfile;
	$bkpath = $curdir.$bizname."/".$bfile."/"."0";
	runlog(__FILE__.":".__LINE__.": file $path prepare open!");
	if (is_file($path))
		return file_get_contents($path);
	else if (is_file($bkpath))
		return file_get_contents($bkpath);
	else
		runlog(__FILE__.":".__LINE__.": file $path not exist!");
	return "";

}

function do_cdxx($bizid, $idx, $cdxx)
{
	global $curdir;
	$path = $curdir."self_test/".$bizid;
	if (file_exists($path) === false)
		mkdir($path, 0777);
	$file = $path."/".$idx;
	file_put_contents($file, $cdxx);
}


?>
