CREATE TABLE `tel_user` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL DEFAULT '',
  `sex` CHAR(1) NOT NULL DEFAULT '',
  `tel` VARCHAR(16) DEFAULT NULL,
  `money` int default 0,
   `point` int default 0,
  `modtime` VARCHAR(16) NOT NULL DEFAULT '',
  PRIMARY KEY(`id`),
  unique key(tel)
);
