CREATE TABLE `wx_userinfo` (   `id` int(11) NOT NULL AUTO_INCREMENT,   bizname varchar(32) DEFAULT NULL, `fakeid` varchar(32) DEFAULT NULL,   `nickname` varchar(32) DEFAULT NULL,   `subscribe_type` varchar(1024) DEFAULT NULL,   `subscribe_distribit` varchar(1024) DEFAULT NULL,   `modtime` varchar(16) NOT NULL,   `status` varchar(1) NOT NULL,   `accept_open` varchar(1) NOT NULL,   `accept_other` varchar(1) NOT NULL,   `chatflag` varchar(1) NOT NULL, `chat_expire` varchar(16) DEFAULT NULL, `wx_username` varchar(128) DEFAULT NULL,   `lastX` varchar(32) DEFAULT NULL,   `lastY` varchar(32) DEFAULT NULL,   PRIMARY KEY (`id`),   UNIQUE KEY `fakeid` (`fakeid`, `bizname`), UNIQUE KEY `wx_username` (`wx_username`, `bizname`), UNIQUE KEY `nickname` (`nickname`, `bizname`) ) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8;

CREATE TABLE `open_biz` (   `id` int(11) NOT NULL AUTO_INCREMENT,   bizname varchar(32) DEFAULT NULL, username varchar(32) DEFAULT NULL, passwd varchar(32) DEFAULT NULL, `filename` varchar(256) DEFAULT NULL,   `starttime` varchar(16) DEFAULT NULL,   `last_paytime` varchar(16) DEFAULT NULL,   `pushmsg_file` varchar(256) DEFAULT NULL,  `status` varchar(1) NOT NULL, PRIMARY KEY (`id`),   UNIQUE KEY `bizname` (`bizname`) ) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8;
