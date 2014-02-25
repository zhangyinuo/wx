create database biz_p1;

use biz_p1;

CREATE TABLE `biz_user` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`bizname` VARCHAR(64) NOT NULL DEFAULT '',
	`name` VARCHAR(64) NOT NULL DEFAULT '',
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
	`regtime` VARCHAR(16) NOT NULL DEFAULT '',
	PRIMARY KEY(`id`),
	unique wxkey(bizname, fakeid),
	unique wxkey2(bizname, wx_username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

