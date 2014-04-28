<?php

include "./snoopy.class.php";
$send_snoopy = new Snoopy;
$submit = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxfe7bc87fda8bd45d&secret=ba78461c30ad7340758aa9009bdecec8";

$send_snoopy->fetch($submit);

$ret = $send_snoopy->results;

echo "$ret";

?>
