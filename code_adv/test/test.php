<?php 

$ROOTDIR=dirname(__FILE__)."/../";
include "./snoopy.class.php";

$str = file_get_contents("msg");

$pexe = $ROOTDIR."token/phantomjs";

$js = $ROOTDIR."token/weixin_sendmsg.js";

$token = "xwFD3SeGNUpPEuMLH7nJP1ltZmfVyTb9x6wwmyo73qUb6a3oKvDVrLjjABg3UkWLbEh4SNTRj8C9Vx03PeYsKPmuePGgxVx6YMrpbZcNqztDpLSzDxL3nV3fsLSeuyy-1URlbwok8-8pTPtjvUCZEQ";

$sstr = $str;

$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$token";

$ret = http_post_data($url, $sstr);
echo $ret;

//popen ("$pexe $js $token '$sstr' > ./result ", "r");
?>

