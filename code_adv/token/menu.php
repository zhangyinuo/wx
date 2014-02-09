<?php 

$ROOTDIR=dirname(__FILE__)."/../";

$str = file_get_contents("menu");

$token = "xwFD3SeGNUpPEuMLH7nJP1ltZmfVyTb9x6wwmyo73qUb6a3oKvDVrLjjABg3UkWLbEh4SNTRj8C9Vx03PeYsKPmuePGgxVx6YMrpbZcNqztDpLSzDxL3nV3fsLSeuyy-1URlbwok8-8pTPtjvUCZEQ";

$sstr = $str;

$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$token";

$ret = http_post_data($url, $sstr);
echo $ret;

?>

