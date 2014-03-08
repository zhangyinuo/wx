<?php 
$str = file_get_contents("str");
$postObj = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);

$s = $postObj->body->pre;
$r = json_decode($s, true);
$a = $r["access_token"];
$t = $r["expires_in"];
$a1 = $r["expires_in1"];
if (strlen($a1) < 2)
	echo "false\n";

echo $a." ".$t;

?>

