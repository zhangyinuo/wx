<?php 

$ROOTDIR=dirname(__FILE__)."/../";

require_once($ROOTDIR."log/log.php");

function get_item($title, $desc, $picurl, $url)
{
	$msg = "<item>
		<Title><![CDATA[%s]]></Title>
		<Description><![CDATA[%s]]></Description>
		<PicUrl><![CDATA[%s]]></PicUrl>
		<Url><![CDATA[%s]]></Url>
		</item>";

	return sprintf($msg, $title, $desc, $picurl, $url); 

}

$picurl1 = "http://14.17.117.32:8096/pic/1.jpg";
$picurl2 = "http://14.17.117.32:8096/pic/2.jpg";

$title1 = "跑车";
$title2 = "跑车";

$desc1 = "跑车";
$desc2 = "跑车";

$url1 = "http://product.auto.163.com/product/000BNaIS.html";
$url2 = "http://product.auto.163.com/product/000BNaIS.html";

$item1 = get_item($title1, $desc1, $picurl1, $url1);
$item2 = get_item($title2, $desc2, $picurl2, $url2);

$msg = "<MsgType><![CDATA[news]]></MsgType>
	<ArticleCount>2</ArticleCount>
	<Articles>
	%s
	%s
	</Articles>
	</xml>";

$content = sprintf($msg, $item1, $item2);
$bizname = "self_test";
$file = $ROOTDIR."file/".$bizname."/filename";
file_put_contents($file, $content);
?>

