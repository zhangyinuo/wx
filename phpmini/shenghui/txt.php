<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>php</title>
</head>

<body>


<?php

	$i =1;
	while($i<=5){
		echo "the number is:" . $i . "</br>";
		$i++;
	}
$i=0;
do{
	$i++;
	echo "the number is two:" . $i . "</br>";
}while($i<5)
for($i=0;$i<5;$i++){
	echo "the number is :" . $i . "</br>";
}
//Êä³öhelloworld5´Î
ifor($i=0;$i<5;$i++){
	echo "hello world" . "</br>";
}
$arr = array("one","tow","three");
foreach($arr as $value){
	echo "zhl" . $value . "</br>";
}
function writeMyName(){
	echo "My Name Is ZengHuaLiang";
}
writeMyName();
function writeMyName()
  {
	    echo "David Yang";
  }
echo "Hello world!<br />";
echo "My name is ";
writeMyName();
echo ".<br />That's right, ";
writeMyName();
echo " is my name.";
function writeMyname(){
	echo "zhl";
}
echo "hello world£¡</br>";
echo "My name is ";
writeMyname();
echo "is my name."
function writeName($fname,$pun){
	echo $fname . "yang" . $pun . "</br>";
}
echo "My name is ";
writeName("John","...");
echo "My name is ";
writeName("David",".");
echo "My name is ";
writeName("Mike","!");
function add($x,$y){
	$total = $x + $y;
	return $total;
}
echo "1 + 16 = " . add(1,16);
 


echo date("y/m/d");
 

//php¹ýÂËÆ÷
$int = "sdaf";

if(!filter_var($int, FILTER_VALIDATE_INT))
	 {
		  echo("Integer is not valid");
     }
else
	 {
		  echo("Integer is valid");
     }
 */

if(!filter_has_var(INPUT_POST, "url"))
	 {
		  echo("Input type does not exist");
     }
else
	 {
		  $url = filter_input(INPUT_POST, "url", FILTER_SANITIZE_URL);
     }

?>

<form action="hello.php" method="get">
Name: <input type="text" name="name" />
Age: <input type="text" name="age" />
<input type="submit" />
</form>



</body>
</html>




