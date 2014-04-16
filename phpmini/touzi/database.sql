CREATE TABLE `users` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL DEFAULT '',
  `password` VARCHAR(45) NOT NULL DEFAULT '',
  `sex` CHAR(1) NOT NULL DEFAULT '',
  `mail` VARCHAR(255) NOT NULL DEFAULT '',
  `photo` VARCHAR(255),
  `tel` VARCHAR(45),
  `web` VARCHAR(255),
  `birthday` VARCHAR(255),
  `inter` TEXT,
  `intro` TEXT,
  `reg_time` VARCHAR(19) NOT NULL DEFAULT '',
  PRIMARY KEY(`id`)
);
