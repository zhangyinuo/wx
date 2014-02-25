create database biz_p1;

use biz_p1;

CREATE TABLE `biz_user` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`bizname` VARCHAR(64) NOT NULL DEFAULT '',
<<<<<<< HEAD
	`cname` VARCHAR(64) NOT NULL DEFAULT '',
=======
	`name` VARCHAR(64) NOT NULL DEFAULT '',
>>>>>>> e2a0f5043fd44a33281989a9a99da7e76d42d21a
	`password` VARCHAR(32) NOT NULL DEFAULT '',
	`mail` VARCHAR(255) NOT NULL DEFAULT '',
	`tel` VARCHAR(16),
	`regtime` VARCHAR(16) NOT NULL DEFAULT '',
	PRIMARY KEY(`id`),
	unique bizkey(bizname)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `wx_user` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`bizname` VARCHAR(64) not null DEFAULT 'test',
	`wx_username` VARCHAR(32) DEFAULT NULL,
	`fakeid` VARCHAR(32) DEFAULT NULL,
<<<<<<< HEAD
	`lreqmsg` text DEFAULT NULL,
	`lrspmsg` text DEFAULT NULL,
	`lreqtime` VARCHAR(16) DEFAULT NULL,
=======
>>>>>>> e2a0f5043fd44a33281989a9a99da7e76d42d21a
	`regtime` VARCHAR(16) NOT NULL DEFAULT '',
	PRIMARY KEY(`id`),
	unique wxkey(bizname, fakeid),
	unique wxkey2(bizname, wx_username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

