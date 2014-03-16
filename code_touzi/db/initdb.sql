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
	`flag` int default 0,
	`un_modtime` varchar(16) default null,
	`sadmin` varchar(256) default "nothing",
	`atime` varchar(16) default null,
	`yw_name` varchar(16) default null,
	`yw_msisdn` varchar(16) default null,
	`role` int default 0,
	`dispatch` int default 0,
	PRIMARY KEY (`id`),
	unique key(fakeid),
	unique key(wx_username),
	key(msisdn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE t_wx_location (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`wx_username` varchar(32) not null,
	`modtime` varchar(16) not null,
	`x` varchar(16) default null,
	`y` varchar(16) default null,
	`l` varchar(256) default null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE t_wx_data(
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`wx_username` varchar(32) not null,
	`modtime` varchar(16) not null,
	`k` varchar(16) default null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `open_biz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bizname` varchar(32) DEFAULT NULL,
  `wxname` varchar(32) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `filename` varchar(256) DEFAULT NULL,
  `starttime` varchar(16) DEFAULT NULL,
  `last_paytime` varchar(16) DEFAULT NULL,
  `pushmsg_file` varchar(256) DEFAULT NULL,
  `status` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bizname` (`bizname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `open_biz` VALUES (NULL,'self_test', 'gh_8884f4eb3560', 'wxfe7bc87fda8bd45d','ba78461c30ad7340758aa9009bdecec8','filename','20140101000000','20140101000000','pushmsg_file','1');

