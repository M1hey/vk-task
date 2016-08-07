CREATE DATABASE IF NOT EXISTS vk_task
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `users` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `login`         VARCHAR(32)  NOT NULL,
  `username`      VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(60)  NOT NULL,
  `account_type`  INT(11)      NOT NULL,
  `balance`       INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_uindex` (`login`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8;