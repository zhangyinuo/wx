<!DOCTYPE html> 
<html>
		<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
				<title>wisdom</title>
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.css" />
				<script src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
				<script src="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script>
		</head>
<script>
function viewProfile(){    
	if (typeof WeixinJSBridge != "undefined" && WeixinJSBridge.invoke){    
		WeixinJSBridge.invoke('profile',{    
			'username':'gh_4a8c72a3f21a',    /* 你的公众号原始ID */
				'scene':'57'    
		});    
	}    
}
</script>

		<body>
<?php
$file="../ueditor/php/html/wisdom/".$_GET['type']."/".$_GET['id'].".html";
	include($file)
?>
		</body>
</html>
