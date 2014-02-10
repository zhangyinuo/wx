<?php
$info;
$result = http_get("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxfe7bc87fda8bd45d&secret=ba78461c30ad7340758aa9009bdecec8", array("timeout"=>1), $info);


?>
