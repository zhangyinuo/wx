<?php

define("datadir", "./");

class wx_talk
{
	public function __construct()
	{
	}

	public function check_valid($weixin_open, $wx_username, $msg, $db)
	{
		if (black_word($msg))
			return NULL;
		return get_flag_nickname($weixin_open, $wx_username, $db);
	}

	public function send_msg_2_fid($account, $passwd, $fid, $msg)
	{
	}
}
