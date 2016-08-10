CREATE DATABASE IF NOT EXISTS vk_task
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `users` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(60)  NOT NULL,
  `account_type`  INT(11)      NOT NULL,
  `balance`       INT(11)      NOT NULL DEFAULT '0',
  `login`         VARCHAR(32)  NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_uindex` (`login`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8;

CREATE TABLE `auth_tokens` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `validator_hash` VARCHAR(64)      DEFAULT NULL,
  `token`          VARCHAR(64)      DEFAULT NULL,
  `user_id`        INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_tokens_user_id_uindex` (`user_id`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8;

CREATE TABLE `orders` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `title`         VARCHAR(60)  NOT NULL,
  `reward`        INT(11)      NOT NULL,
  `employer_id`   INT(11)      NOT NULL,
  `employer_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8