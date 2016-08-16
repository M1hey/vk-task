INSERT INTO users (login, username, password_hash, account_type, balance)
VALUES
  ('worker', 'Исполнитель Вася', '$2y$12$4mjTejyfKrL5dLJPuztWXeX0owfhx8sBkqF/tkyWMFHlEjgcgnEfy', 1, 0),
  ('employer', 'Заказчик Петя', '$2y$12$4mjTejyfKrL5dLJPuztWXeX0owfhx8sBkqF/tkyWMFHlEjgcgnEfy', 2, 1000000);

INSERT INTO system_account (id, balance, commission_percent) VALUES (1, 0, 5);
