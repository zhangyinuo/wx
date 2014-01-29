<?php
define("TOKEN", "jingchun_dev_weixin");

function open_log($buf)
{
	$logfile = "/home/php/logs/weeixin_dev.log";
	$logfd = fopen($logfile, "a+");
	if ($logfd === FALSE)
	{
		echo "open $logfile err!\n";
		die ('Failed to fopen log');
	}
	fwrite($logfd, date('YmdHis').":".$buf);
	fclose($logfd);
}

class wechatCallbackapiTest
{
	public function valid()
	{
		$echoStr = $_GET["echostr"];

		//valid signature , option
		if($this->checkSignature()){
			$this->responseMsg();
			return true;
		}
		else
			return false;
	}

	public function responseMsg()
	{
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

		//extract post data
		if (!empty($postStr)){

			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$type = trim($postObj->MsgType);
			$event = trim($postObj->Event);
			$keyword = trim($postObj->Content);
			$time = time();
			open_log("type:[".$type."]\n");
			open_log("event:[".$event."]\n");
			open_log("keyword:[".$keyword."]\n");
			$textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
				</xml>";          
			if (strcasecmp("event", $type) == 0)
			{
				open_log("event:".$type."\n");
				$msgType = "text";
				if (strcasecmp("subscribe", $event) == 0)
					$contentStr = "欢迎\n";
				else
					$contentStr = "欢迎下次订阅\n";
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			}
			else	
			{
				open_log("event:".$type."\n");
				return;
				$msgType = "text";
				$contentStr = "您的消息已经受理，稍后给您答复!\n";
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			}

		}
	}

	public function get_User_Time_Type_Content(&$user, &$time, &$type, &$content)
	{
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$user = $postObj->FromUserName;
		$time = $postObj->CreateTime;
		$type = $postObj->MsgType;
		$content = trim($postObj->Content);
	}

	private function checkSignature()
	{
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];	

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
