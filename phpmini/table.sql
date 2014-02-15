CREATE TABLE `tel_user` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `bizname` VARCHAR(64) not null DEFAULT 'test',
  `fakeid` VARCHAR(32) DEFAULT NULL,
  `tel` VARCHAR(16) DEFAULT NULL,
  `sex` CHAR(1) NOT NULL DEFAULT '0',
  `money` float default 0,
  `point` INTEGER UNSIGNED default 0,
  `regtime` VARCHAR(16) NOT NULL DEFAULT '',
  `modtime` VARCHAR(16) NOT NULL DEFAULT '',
  PRIMARY KEY(`id`),
  unique telkey(bizname, tel),
  unique wxkey(bizname, fakeid)
);
