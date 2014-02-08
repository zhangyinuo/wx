<?php 

$ROOTDIR=dirname(__FILE__)."/../";
include "./snoopy.class.php";

$str = file_get_contents("menu");

$pexe = $ROOTDIR."token/phantomjs";

$js = $ROOTDIR."token/weixin_send_c.js";

$token = "3ogPzSoJBk44LzGN4h7eDDl-WR8jH9NBHBkQbqGzbUQAcrkM2JdnBeZsooLXmGK6ILEY050iwqT6t2v3DRocmzfRD_dxb13NfFpIYoC9Fb2VJh0M-oEB36DOATpIn16_JRnXO7R4JadfIMirTP8Lfg";

$send_snoopy = new Snoopy; 

$submit = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$token;
$ret = $send_snoopy->submit($submit,$str);
echo $ret;
?>

