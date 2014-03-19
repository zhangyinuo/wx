<?php 
$file = "/data/app/wx/code/ftok/sub_queue_self_test ";
$key = ftok($file, $p);
$q = msg_get_queue($key, 0666);

echo $q;

?>

