CREATE DATABASE IF NOT EXISTS vk_task
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `auth_tokens` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `validator_hash` VARCHAR(64)      DEFAULT NULL,
  `token`          VARCHAR(64)      DEFAULT NULL,
  `user_id`        INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_tokens_user_id_uindex` (`user_id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8;

CREATE TABLE `orders` (
  `id`            INT(11)      NOT NULL                                                AUTO_INCREMENT,
  `title`         VARCHAR(60)  NOT NULL,
  `reward`        INT(11)      NOT NULL,
  `employer_id`   INT(11)                                                              DEFAULT NULL,
  `employer_name` VARCHAR(255) NOT NULL,
  `worker_id`     INT(11)                                                              DEFAULT '0',
  `status`        ENUM ('created', 'paid', 'reserved', 'reward_credited', 'completed') DEFAULT 'created',
  `comission`     INT(11)                                                              DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

CREATE TABLE `users` (
  `id`              INT(11)      NOT NULL AUTO_INCREMENT,
  `username`        VARCHAR(255) NOT NULL,
  `password_hash`   VARCHAR(60)  NOT NULL,
  `account_type`    INT(11)      NOT NULL,
  `balance`         INT(11)      NOT NULL DEFAULT '0',
  `login`           VARCHAR(32)  NOT NULL,
  `reserved_amount` INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_uindex` (`login`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8;

INSERT INTO users (login, username, password_hash, account_type, balance)
VALUES ('worker', 'Исполнитель Вася', '$2y$12$4mjTejyfKrL5dLJPuztWXeX0owfhx8sBkqF/tkyWMFHlEjgcgnEfy', 1, 0),
  ('employer', 'Заказчик Петя', '$2y$12$4mjTejyfKrL5dLJPuztWXeX0owfhx8sBkqF/tkyWMFHlEjgcgnEfy', 2, 1000000);

CREATE TABLE `system_account` (
  `id`                 INT(11) NOT NULL,
  `balance`            INT(11) NOT NULL DEFAULT '0',
  `commission_percent` INT(11) NOT NULL DEFAULT '5',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO system_account (id, balance, commission_percent) VALUES (1, 0, 5);

CREATE TABLE connection_counters
(
  remote_addr           VARCHAR(32) PRIMARY KEY NOT NULL,
  requests_count        INT(11) DEFAULT '1'     NOT NULL,
  last_access_timestamp INT(11)
)
  ENGINE = MEMORY
  DEFAULT CHARSET = utf8;