create database wx_touzi;

use wx_touzi;

CREATE TABLE t_wx_info (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`fakeid` varchar(32) not null,
	`wx_username` varchar(32) not null,
	`modtime` varchar(16) not null,
	`step1` varchar(16) default null,
	`step2` varchar(16) default null,
	`step3` varchar(16) default null,
	`step4` varchar(16) default null,
	`step5` varchar(16) default null,
	`step6` varchar(16) default null,
	`lasttime` int ,
	`lastindex` int default 0,
	`msisdn` varchar(16) default null,
	PRIMARY KEY (`id`),
	unique key(fakeid),
	unique key(wx_username)
	key(msisdn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE t_wx_location (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`wx_username` varchar(32) not null,
	`modtime` varchar(16) not null,
	`x` varchar(16) default null,
	`y` varchar(16) default null,
	PRIMARY KEY (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

