<?php

require_once 'db.php';
require_once dirname(__DIR__) . '/config/system_config.php';

function complete_order($order_id, $order_amount, &$user) {
    global $system_comission_percent;
    $system_comission = intval($order_amount * $system_comission_percent / 100);

    $order_reserved = query_multiple_params(USERS_DB_MASTER,
        "UPDATE orders
            SET worker_id = ?, status = 'reserved', comission = ? 
            WHERE id = ? AND status = 'created' AND orders.reward = ?",
        'ii', $user['id'], $system_comission, $order_id, $order_amount);

    if (!$order_reserved) {
        return false;
    }

    // add money to account
    $money_added_to_user = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users, orders 
                SET 
                  users.balance = users.balance + orders.reward - orders.comission, 
                  orders.status = 'user_have_money'
                WHERE users.id = ? 
                AND orders.status = 'reserved' AND orders.id = ?",
        'ii', $user['id'], $order_id);

    if (!$money_added_to_user) {
        return false;
    }

    // add comission to system
    $system_operation_completed = query_multiple_params(USERS_DB_MASTER,
        "UPDATE system_account, orders 
                SET 
                  system_account.balance = system_account.balance + orders.comission,
                  orders.status = 'completed'
                WHERE orders.id = ? AND orders.status = 'user_have_money'
                  AND system_account.id = 0", 'i', $order_id);

    if (!$system_operation_completed) {
        return false;
    }

    return $order_amount - $system_comission;
}

function create_order($employer_id, $employer_name, $title, $amount) {
    global $system_comission_percent;

    // TODO!!!! how would we process a transaction via multiple dbs?
    $money_withdrawed = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users
            SET balance = balance - ?,
            reserved_amount = ?
            WHERE id = ? AND balance >= ?",
        'iiii', $amount, $amount, $employer_id, $amount);

    if (!$money_withdrawed) {
        return false;
    }

    $system_comission = intval($amount * $system_comission_percent / 100);

    $order_id = query_multiple_params(USERS_DB_MASTER,
        "INSERT INTO orders (title, reward, employer_id, employer_name, comission) 
            VALUES (?,?,?,?,?)",
        'siisi', $title, $amount, $employer_id, $employer_name, $system_comission);

    if (!$order_id) {
        return false;
    }

    $money_withdrawed = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users, orders
            SET 
              users.reserved_amount = users.reserved_amount - orders.reward,
              orders.status = 'paid' 
            WHERE users.id = ? 
              AND users.reserved_amount >= orders.reward
              AND orders.id = ?
              AND orders.status = 'created'",
        'ii', $employer_id, $order_id);

    return $money_withdrawed;
}

function get_orders() {
    // todo limit, pagination
    return query(USERS_DB_SLAVE,
        "SELECT id, title, reward, employer_name FROM orders LIMIT 15",
        '', null);
}

function get_orders_by_emp_id($employer_id) {
    return query(USERS_DB_SLAVE,
        "SELECT id, title, reward FROM orders WHERE employer_id = ?",
        'i', $employer_id);
}