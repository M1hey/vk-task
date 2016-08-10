CREATE DATABASE IF NOT EXISTS vk_task
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `validator_hash` varchar(64) DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_tokens_user_id_uindex` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `completed_orders` (
  `id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `title` varchar(60) NOT NULL,
  `amount` int(11) DEFAULT NULL,
  `comission` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `reward` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `employer_name` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `system_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(60) NOT NULL,
  `account_type` int(11) NOT NULL,
  `balance` int(11) NOT NULL DEFAULT '0',
  `login` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_uindex` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO users (login, username, password_hash, account_type, balance)
VALUES ('worker','Исполнитель Вася', '$2y$12$4mjTejyfKrL5dLJPuztWXeX0owfhx8sBkqF/tkyWMFHlEjgcgnEfy', 1, 0),
  ('employer','Заказчик Петя', '$2y$12$4mjTejyfKrL5dLJPuztWXeX0owfhx8sBkqF/tkyWMFHlEjgcgnEfy', 2, 0);