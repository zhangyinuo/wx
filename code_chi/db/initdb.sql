create database wx_chi;

use wx_chi;

 CREATE TABLE `wx_userinfo` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `bizname` varchar(32) DEFAULT NULL,
	  `fakeid` varchar(32) DEFAULT NULL,
	  `nickname` varchar(32) DEFAULT NULL,
	  `subscribe_type` varchar(1024) DEFAULT NULL,
	  `subscribe_distribit` varchar(1024) DEFAULT NULL,
	  `modtime` varchar(16) NOT NULL,
	  `status` varchar(1) NOT NULL,
	  `flag` varchar(1) NOT NULL,
	  `accept_other` varchar(1) NOT NULL,
	  `chatflag` varchar(1) NOT NULL,
	  `msisdn` varchar(16) DEFAULT NULL,
	  `wx_username` varchar(128) DEFAULT NULL,
	  `lastX` varchar(32) DEFAULT NULL,
	  `lastY` varchar(32) DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `fakeid` (`fakeid`,`bizname`),
	  UNIQUE KEY `wx_username` (`wx_username`,`bizname`),
	  UNIQUE KEY `nickname` (`nickname`,`bizname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8


CREATE TABLE t_biz_info (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`bizname` varchar(32) not null,
	`picroot` varchar(32) not null,
	`bizpasswd` varchar(16) not null,
	`msisdn` varchar(16) default null,
	`flag` int default 0,
	unique key(id),
	key(msisdn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/**
flag :
0: ok
1: pause
2: stop 
**/

CREATE TABLE t_order_info (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`orderinfo` varchar(32) not null,
	`msisdn` varchar(16) not null,
	`bizmsisdn` varchar(16) not null,
	unique key(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE t_select_info (
	`fakeid` varchar(32) not null,
	`step1` varchar(16) default null,
	`step2` varchar(16) default null,
	`step3` varchar(16) default null,
	`step4` varchar(16) default null,
	`step5` varchar(16) default null,
	`step6` varchar(16) default null,
	`lasttime` int ,
	`lastindex` int default 0,
	unique key(fakeid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

