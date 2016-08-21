CREATE TABLE `auth_tokens` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `validator_hash` VARCHAR(64)      DEFAULT NULL,
  `token`          VARCHAR(64)      DEFAULT NULL,
  `user_id`        INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_tokens_user_id_uindex` (`user_id`)
)
  ENGINE = myisam
  AUTO_INCREMENT = 12
  DEFAULT CHARSET = utf8;

CREATE TABLE `connection_counters` (
  `remote_addr`           VARCHAR(32) NOT NULL,
  `requests_count`        INT(11)     NOT NULL DEFAULT '1',
  `last_access_timestamp` INT(11)              DEFAULT NULL,
  PRIMARY KEY (`remote_addr`)
)
  ENGINE = MEMORY
  DEFAULT CHARSET = utf8;

CREATE TABLE `orders` (
  `id`            INT(11)      NOT NULL                             AUTO_INCREMENT,
  `title`         VARCHAR(60)  NOT NULL,
  `reward`        INT(11)      NOT NULL,
  `employer_id`   INT(11)                                           DEFAULT NULL,
  `employer_name` VARCHAR(255) NOT NULL,
  `worker_id`     INT(11)                                           DEFAULT '0',
  `status`        ENUM ('created', 'paid', 'reserved', 'completed') DEFAULT 'created',
  `commission`    INT(11)                                           DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = myisam
  AUTO_INCREMENT = 82
  DEFAULT CHARSET = utf8;

CREATE TABLE `system_account` (
  `id`                 INT(11) NOT NULL,
  `balance`            INT(11) NOT NULL DEFAULT '0',
  `commission_percent` INT(11) NOT NULL DEFAULT '5',
  PRIMARY KEY (`id`)
)
  ENGINE = myisam
  DEFAULT CHARSET = utf8;

CREATE TABLE `system_transactions` (
  `id`         INT(11)                       NOT NULL AUTO_INCREMENT,
  `order_id`   INT(11)                       NOT NULL,
  `commission` INT(11)                       NOT NULL,
  `status`     ENUM ('created', 'completed') NOT NULL DEFAULT 'created',
  PRIMARY KEY (`id`)
)
  ENGINE = myisam
  DEFAULT CHARSET = utf8;

CREATE TABLE `users` (
  `id`                     INT(11)      NOT NULL AUTO_INCREMENT,
  `username`               VARCHAR(255) NOT NULL,
  `password_hash`          VARCHAR(60)  NOT NULL,
  `account_type`           INT(11)      NOT NULL,
  `balance`                INT(11)      NOT NULL DEFAULT '0',
  `login`                  VARCHAR(32)  NOT NULL,
  `failed_attempts`        INT(11)      NOT NULL DEFAULT '0',
  `last_attempt_timestamp` INT(11)               DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_uindex` (`login`)
)
  ENGINE = myisam
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

CREATE TABLE `users_transactions` (
  `id`             INT(11)                       NOT NULL AUTO_INCREMENT,
  `order_id`       INT(11)                       NOT NULL,
  `worker_id`      INT(11)                       NOT NULL,
  `reward_to_user` INT(11)                       NOT NULL
  COMMENT 'This reward already does not include commission.',
  `status`         ENUM ('created', 'completed') NOT NULL DEFAULT 'created',
  PRIMARY KEY (`id`)
)
  ENGINE = myisam
  DEFAULT CHARSET = utf8;

CREATE TABLE `order_creation_transactions` (
  `id`          INT(11)                              NOT NULL AUTO_INCREMENT,
  `employer_id` INT(11)                              NOT NULL,
  `order_id`    INT(11)                              NOT NULL,
  `amount`      INT(11)                              NOT NULL,
  `status`      ENUM ('order_created', 'order_paid') NOT NULL DEFAULT 'order_created',
  PRIMARY KEY (`id`)
)
  ENGINE = myisam
  AUTO_INCREMENT = 23
  DEFAULT CHARSET = utf8;