create database wx_touzi;

use wx_touzi;

CREATE TABLE t_wx_info (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`fakeid` varchar(32) not null,
	`wx_username` varchar(32) not null,
	`wx_flag` varchar(1) not null default '0',
	`modtime` varchar(16) not null,
	`lasttime` int ,
	`laststep` varchar(16) default null,
	PRIMARY KEY (`id`),
	unique key(fakeid),
	unique key(wx_username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE t_teacher_info (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`name` varchar(32) not null,
	`status` int not null default '1',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE t_course_info (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`name` varchar(32) not null,
	`status` int not null default '1',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE t_class_info (
	`id` int(11) NOT NULL AUTO_INCREMENT, 
	`name` varchar(32) not null,
	`teacherid` int not null,
	`status` int not null default '1',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


