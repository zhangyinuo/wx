<?php 

$ROOTDIR=dirname(__FILE__)."/../";
$curdir=dirname(__FILE__)."/";

require_once($ROOTDIR."log/log.php");

function get_content($bfile)
{
	global $curdir;
	$path = $curdir."file/".$bfile;
	runlog(__FILE__.":".__LINE__.": file $path prepare!");
	if (file_exists($path))
		return file_get_contents($path);
	else
		runlog(__FILE__.":".__LINE__.": file $path not exist!");

}

?>

