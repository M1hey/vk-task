<?php

require_once 'db.php';
require_once dirname(__DIR__) . '/config/system_config.php';

function complete_order($order_id, &$user) {
    execute_in_transaction(USERS_DB, function () use ($order_id, &$user) {
        global $system_comission_percent;

        $order = query_multiple_params(USERS_DB,
            "SELECT id, title, reward, employer_id FROM vk_task.orders
                WHERE id = ?",
            'i', $order_id);

        if (!$order) {
            return false;
        }

        $order_deleted = query_multiple_params(USERS_DB,
            "DELETE FROM vk_task.orders
              WHERE id = ?",
            'i', $order_id);

        if (!$order_deleted) {
            return false;
        }

        $system_comission = $order['reward'] * $system_comission_percent / 100;

        $order_created = query_multiple_params(USERS_DB,
            "INSERT INTO vk_task.completed_orders (id, title, amount, comission, employer_id, worker_id) 
              VALUES (?,?,?,?,?,?)",
            'isiiii', $order_id, $order['title'], $order['reward'], $system_comission, $order['employer_id'], $user['id']);

        if (!$order_created) {
            return false;
        }

        // TODO really it should be named reward, and oredr['reward'] -> amount
        $sum_to_worker = $order['reward'] - $system_comission;

        // add money to account
        $money_added = query_multiple_params(USERS_DB,
            "UPDATE vk_task.users AS vk_user
                SET vk_user.balance = vk_user.balance + ?
                WHERE vk_user.id = ?",
            'ii', $sum_to_worker, $user['id']);


        if (!$money_added) {
            return false;
        }

        $user['balance'] += $sum_to_worker; // usually i don't do it

        // add comission to system
        $system_operation_completed = query_multiple_params(USERS_DB,
            "INSERT INTO system_history(timestamp, amount) VALUES (?,?)", 'ii', time(), $system_comission);

        if (!$system_operation_completed) {
            return false;
        }

        return true;
    });
}

function create_order($employer_id, $employer_name, $title, $amount) {
    // TODO!!!! how would we process a transaction via multiple dbs?
    execute_in_transaction(USERS_DB, function () use ($employer_id, $employer_name, $title, $amount) {
        $money_withdrawed = query_multiple_params(USERS_DB,
            "UPDATE vk_task.users AS vk_user
            SET vk_user.balance = vk_user.balance - ?
            WHERE vk_user.id = ? AND vk_user.balance >= ?",
            'iii', $amount, $employer_id, $amount);

        if (!$money_withdrawed) {
            return false;
        }

        $order_created = query_multiple_params(USERS_DB,
            "INSERT INTO vk_task.orders (title, reward, employer_id, employer_name) 
            VALUES (?,?,?,?)",
            'siis', $title, $amount, $employer_id, $employer_name);

        return $order_created;
    });
}

function get_orders() {
    // todo limit, pagination
    return query(USERS_DB,
        "SELECT id, title, reward, employer_name FROM vk_task.orders LIMIT 15",
        '', null);
}

function get_orders_by_emp_id($employer_id) {
    return query(USERS_DB,
        "SELECT id, title, reward FROM vk_task.orders WHERE employer_id = ?",
        'i', $employer_id);
}