<?php 

function runlog($text){
	file_put_contents(dirname(__FILE__) .'/runlog.log', date("D M j G:i:s T Y")." ".$text."\n",FILE_APPEND);		
};

?>

