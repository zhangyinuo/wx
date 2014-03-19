<?php

$t = array ();

$t['app'] = "wx";

$k = array_keys($t);

if (in_array('app', $k))
	echo "OK\n";
else
	echo "ERROR\n";
?>
