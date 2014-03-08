create database wx_touzi;

use wx_touzi;

CREATE TABLE t_wx_info (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`fakeid` varchar(32) not null,
	`wx_username` varchar(32) not null,
	`modtime` varchar(16) not null,
	`step1` varchar(16) defualt null,
	`step2` varchar(16) defualt null,
	`step3` varchar(16) defualt null,
	`step4` varchar(16) defualt null,
	`step5` varchar(16) defualt null,
	`step6` varchar(16) defualt null,
	`lasttime` int ,
	`lastindex` int default 0,
	PRIMARY KEY (`id`),
	unique key(fakeid),
	unique key(wx_username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

