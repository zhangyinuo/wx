<?php

$cururl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxfe7bc87fda8bd45d&secret=ba78461c30ad7340758aa9009bdecec8";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $cururl);
curl_setopt($ch, CURLOPT_HEADER, false);
$result = curl_exec($ch);


echo "$result";

?>
