<?php

require_once 'db.php';

function complete_order($order_id, $user_id) {
    $order_reserved = query_multiple_params(USERS_DB_MASTER,
        "UPDATE orders
            SET worker_id = ?, status = 'reserved' 
            WHERE id = ? 
              AND status = 'paid'
              AND orders.employer_id != ?",
        'iii', $user_id, $order_id, $user_id);

    if (!$order_reserved) {
        // TODO return new orders list
        return false;
    }

    // order is no more accessible to other users
    // add money to account
    $reward_credited = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users, orders 
                SET 
                  users.balance = users.balance + COALESCE(orders.reward, 0) - COALESCE(orders.comission, 0), 
                  orders.status = 'reward_credited'
                WHERE users.id = ? 
                  AND orders.id = ? 
                  AND orders.status = 'reserved'",
        'ii', $user_id, $order_id);

    if (!$reward_credited) {
        return false;
    }

    // add comission to system
    $order_completed = query_multiple_params(USERS_DB_MASTER,
        "UPDATE system_account, orders 
                SET 
                  system_account.balance = system_account.balance + orders.comission,
                  orders.status = 'completed'
                WHERE orders.id = ? 
                  AND orders.status = 'reward_credited'
                  AND system_account.id = 1", 'i', $order_id);

    if (!$order_completed) {
        return false;
    }

    return $order_completed;
}

function create_order($employer_id, $employer_name, $title, $amount) {
    $system_commission_percent = single_result(query(USERS_DB_SLAVE, "SELECT commission_percent FROM system_account"));

    if (!$system_commission_percent) {
        return false;
    }
    $system_commission_percent = $system_commission_percent['commission_percent'];

    $money_withdrawed = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users
            SET
              balance = balance - ?,
              reserved_amount = reserved_amount + ?
            WHERE id = ? AND balance >= ?",
        'iiii', $amount, $amount, $employer_id, $amount);

    if (!$money_withdrawed) {
        return false;
    }

    $system_comission = intval($amount * $system_commission_percent / 100);

    $order_id = query_multiple_params(USERS_DB_MASTER,
        "INSERT INTO orders (title, reward, employer_id, employer_name, comission) 
            VALUES (?,?,?,?,?)",
        'siisi', $title, $amount, $employer_id, $employer_name, $system_comission);

    if (!$order_id) {
        return false;
    }

    /** По идее эта операция атомарна, хотя это не написано ни в документации, ни в книгах.
     *  Аналогично и логичнее для меня будет сделать так.
     *
     *      AUTOCOMMIT = 0;
     *      BEGIN;
     *      UPDATE users SET users.reserved_amount = users.reserved_amount - ?
     *          WHERE users.id = ? AND users.reserved_amount >= ?;
     *      UPDATE orders SET orders.status = 'paid'
     *          WHERE orders.id = ? AND orders.status = 'created';
     *      COMMIT;
     *
     *  Ну никак иначе без транзакций.
     **/

    /*  VISUAL EXPLAIN показывает, что стоимость такого запроса 1*/
    $order_paid = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users, orders
            SET
              users.reserved_amount = users.reserved_amount - orders.reward,
              orders.status = 'paid'
            WHERE users.id = ?
              AND users.reserved_amount >= orders.reward
              AND orders.id = ?
              AND orders.status = 'created'",
        'ii', $employer_id, $order_id);

    return $order_paid;
}

function get_orders() {
    // todo limit, pagination
    return query(USERS_DB_SLAVE,
        "SELECT id, title, reward, employer_name FROM orders WHERE status = 'paid' LIMIT 15");
}

function get_orders_by_emp_id($employer_id) {
    return query(USERS_DB_SLAVE,
        "SELECT id, title, reward FROM orders WHERE employer_id = ? AND status = 'paid'",
        'i', $employer_id);
}