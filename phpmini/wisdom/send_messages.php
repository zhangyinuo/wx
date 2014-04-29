<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>send msg</title>
</head>

<body>
<?php
error_reporting(0);
session_start();

 include("functions.inc.php");
require_once("/data/app/wx/code_shenghui/file/file.php");
 include("dbconnect.inc.php");
 if($_POST['send']=="å‘é€"){
	 $tel = $_POST['tel'];
	 $sadmin = $_POST['sadmin'];
	 $sql = "select wx_username from t_wx_info";
	 $sql .= " where msisdn = '$tel';";
	 //runlog($sql);
	 $res =mysql_query($sql);
	 if(!$res) {
		 echo mysql_error();
		 die("Êı¾İ¿â³ö´í£¬Çë·µ»ØÖØÊÔ¡£");
	 }

	 $wxname = ""; 
	 while($rows=mysql_fetch_array($res)){
		 $wxname = $rows[0];
		 break;
	 }

	 $msg =$_POST['msg'];   
	 process_web_request($wxname, "web", $msg);
	 //runlog($msg." ".$tel." ".$wxname);
   header("Location:admin.php?m=send_messages_success");
   exit; 
 }
?>
<form id="myForm" action="/shenghui/send_messages.php?type=<?php echo $_GET['type'] ?>&id=<?php echo $_GET['id'] ?>&name=<?php echo $_SESSION['username']; ?>" method="post">
<script type="text/plain" id="myEditor"></script>

<div>
   <div class=form-item">
      <label for="send_tel">ç”µè¯å·ç </label>
         <span><?php echo $_GET['tel']; ?></span>
    </div>
      <?php
        echo"<input type='hidden' name='tel' value='{$_GET['tel']}'/>"
      ?>
         <div class="form-item">
         <label for="send-sadmin">å‘é€å†…å®¹</label>
            <textarea name="msg" id="msg" rows=3 cols=20 onclick="this.innerHTML=''"/></textarea>
         </div>
<br/>
<br/>
     <div>
       <input type="submit" name="send" id="send" value="å‘é€" class="form-submit" onclick=""/>
     </div>
</div>
</form>
</body>
</html>
