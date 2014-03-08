create database wx_touzi;

use wx_touzi;

CREATE TABLE t_wx_info (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`fakeid` varchar(32) not null,
	`wx_username` varchar(32) not null,
	`modtime` varchar(16) not null,
	`lasttime` int ,
	`laststep` varchar(16) default null,
	PRIMARY KEY (`id`),
	unique key(fakeid),
	unique key(wx_username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

