<?php 
require_once("/home/jingchun.zhang/svn/sys_dev/net_monitor/wx_dev/wechatext.class.php");
$up_queue_file1 = dirname(__FILE__) ."/"."up_queue1";
$down_queue_file1 = dirname(__FILE__) ."/"."down_queue1";

$helpinfo = "七天酒店，点击网址注册会员:http://news.163.com/index.html\n".
	"关键字查询\n".
	"1,地�\n".
	"2,会员\n".
	"3,会员�\n";


$detailinfo = array();

function init_detail()
{
	global $detailinfo;
	$detailinfo[1] = "xxxx";
	$detailinfo[2] = "yyyy";
	$detailinfo[3] = "zzzz";
}

function runlog($text){
	file_put_contents(dirname(__FILE__) .'/runlog.log', date("D M j G:i:s T Y")." ".$text."\n",FILE_APPEND);		
};

function put_up_msg($msg, $q)
{
	msg_send($q, 1, $msg);
}

function get_dist($lng1, $lat1, $lng2 ,$lat2)
{
	$r = 6371.137;
	$dlat = deg2rad($lat2 - $lat1);
	$dlng = deg2rad($lng2 - $lng1);

	$a = pow(sin($dlat / 2), 2) +
		cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
		pow(sin($dlng / 2), 2);

	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

	return $r * $c;
}

function is_match($content, $time, $infile)
{
	$needle = $time;
	runlog ("$time $content\n");
	$result = file_get_contents($infile);
	$result = stristr($result, "wx.cgiData = ");
	$result = substr($result, strlen("wx.cgiData = "), -1);
	$result = stristr($result, "{\"msg_item\":");
	$result = substr($result, strlen("{\"msg_item\":"), -1);
	$endpos = stripos($result, "}]}}");
	$result = substr($result, 0, $endpos+2);
	$arr = json_decode($result, 1);
	foreach ($arr as $subarr)
	{
		if (strcmp($subarr["content"], $content))
			continue;
		if (strcmp($subarr["date_time"], $time))
			continue;
		return true;
	}
	return false;
}

function get_fakeid_from_msg($mondb, &$fakeid, $user1, $time, $content)
{
	$file = "/home/jingchun.zhang/svn/sys_dev/net_monitor/wx_dev/getlist/userlist";
	$pexe = "/home/jingchun.zhang/svn/sys_dev/net_monitor/wx_dev/getlist/pj";
	$js = "/home/jingchun.zhang/svn/sys_dev/net_monitor/wx_dev/getlist/weixin_fid.js";

	$f = 0;
	$handle = @fopen($file, "r");
	if ($handle) {
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			$fid = substr($buffer, 0, -1);
			if (is_numeric ($fid) === false)
				continue;
			if (strlen($fid) < 3)
				continue;
			if (is_exist_fakeid($mondb, $fid) === true)
				continue;
			$dstfile = "/tmp/".$fid.".wx";
			runlog("popen $pexe $js $fid > $dstfile");
			$fp = popen("$pexe $js $fid > $dstfile", "r");
			if ($fp)
				pclose($fp);
			if(is_match($content, $time, $dstfile) === true)
			{
				$f = 1;
				unlink($dstfile);
				$fakeid = $fid;
				break;
			}
			unlink($dstfile);
		}
		fclose($handle);
	}
	if ($f === 1)
		return true;
	return false;
}

function cmd_maskip($c, $monlnk)
{
}

function cmd_maskidc($c, $monlnk)
{
}

function cmd_idctraffic($c, $monlnk)
{
	$nginfile = "/home/jingchun.zhang/3rd/nginx/html/img/".$c.".png";
	return "http://113.105.245.83:8090/img/".$c.".png";
//	$ngoutfile = "/home/jingchun.zhang/3rd/nginx/html/idcimg/".$c.".png";
//	unlink($ngoutfile);
//	if (link($nginfile, $ngoutfile) === true)
//		return "http://113.105.245.83:8090/idcimg/".$c.".png";
//	return "ERROR $nginfile to $ngoutfile";

}

function cmd_process($c, $monlnk)
{
}

function cmd_test($monlnk)
{
	$result = mysql_query("select column_name from information_schema.columns  where table_name = 't_ip_last_update';", $monlnk);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	$items = array();
	$i = 0;

	while($row=mysql_fetch_array($result)) 
	{
		array_push($items, $row[0]);
	}
	mysql_free_result($result);

	$count = count($items);

	echo "$count  $i\n";

	$i = 0;
	while ($i < $count)
	{
		$item = $items[$i];
		$i++;
		echo "$item\n";
	}
}

function cmd_getinfo($c, $monlnk)
{
	runlog("cmd_getinfo ".$c);


	$result = mysql_query("select column_name from information_schema.columns  where table_name = 't_ip_last_update';", $monlnk);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	$items = array();
	$i = 0;

	while($row=mysql_fetch_array($result)) 
	{
		array_push($items, $row[0]);
	}
	mysql_free_result($result);
	$count = count($items);

	$result = mysql_query("select * from t_ip_last_update where First_if like '%,$c,%' or second_if like '%,$c,%';", $monlnk);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	$ret = "";
	$i = 0;

	if($row=mysql_fetch_array($result)) 
	{
		while ($i < $count)
		{
			if (stristr($items[$i], $cmd))
				$ret = $ret.$items[$i].":".$row[$i]."\n";
			$i++;
		}
	}
	mysql_free_result($result);
	return $ret;
}

function content_convert($content, $monlnk)
{
	global $helpinfo;
	global $detailinfo;
	init_detail();

	$f = substr($content, 0, 1);
	if ($f >= 1 && $f <= 3)
	{
		runlog("content_convert:".$f.":".$detailinfo[$f]);
		return $detailinfo[$f];
	}
	return $helpinfo;
}

function is_exist_fakeid($mondb, $fakeid)
{
	$flag = false;
	$result = mysql_query("select wx_username from wx_userinfo where fakeid = '$fakeid'", $mondb);
	if ($result === false)
	{
		runlog("query username from fakeid is null:".$fakeid);
		return $flag;
	}
	while($row=mysql_fetch_array($result)) 
	{
		$flag = true;
		break;
	}
	mysql_free_result($result);
	return $flag;

}

function process_up_msg($fakeid, $content, $q, $monlnk)
{
	$content = content_convert($content, $monlnk);
	$msg = $fakeid."&&".$content;
	msg_send($q, 1, $msg);
}

function update_user_info($fakeid, $user, $mondb)
{
	$result = mysql_query("insert into wx_userinfo values ( NULL, '$fakeid', '', '', '', '', '', '$user', '' ,'')", $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}
}

function get_fakeid_from_db($username, $mondb)
{
	$result = mysql_query("select fakeid from wx_userinfo where wx_username = '$username';", $mondb);
	if ($result === false)
	{
		runlog("Could not query:" . mysql_error());
		die("Could not query:" . mysql_error());
	}

	$ret = false;

	while($row=mysql_fetch_array($result)) 
	{
		$ret = $row[0];
		break;
	}
	mysql_free_result($result);
	return $ret;
}

function init_q(&$q, $file, $p)
{
	if (touch($file))
	{
		$key = ftok($file, $p);
		$q = msg_get_queue($key, 0777);
		return true;
	}
	runlog("ERR to touch $file!");
	return false;
}

function get_User_Time_Type_Content(&$user, &$time, &$type, &$content, $postStr)
{
	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
	$user = $postObj->FromUserName;
	$time = $postObj->CreateTime;
	$type = $postObj->MsgType;
	if ($type == "text")
		$content = trim($postObj->Content);
	if ($type == "location")
		$content = "location";
}
?>

