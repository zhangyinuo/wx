<?php
$file = "../ftok/dispatch_queue_self_test";
$key = dechex(ftok($file, "p"));
echo "$file $key\n\n";

$file = "../ftok/down_queue_self_test";
$key = dechex(ftok($file, "p"));
echo "$file $key\n\n";

$file = "../ftok/up_queue_self_test";
$key = dechex(ftok($file, "p"));
echo "$file $key\n\n";

$file = "../ftok/sub_queue_self_test";
$key = dechex(ftok($file, "p"));
echo "$file $key\n\n";

?>
