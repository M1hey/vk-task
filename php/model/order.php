<?php

require_once 'db.php';

function create_order($employer_id, $employer_name, $title, $amount) {
    // TODO!!!! how would we process a transaction via multiple dbs?
    if (begin_transaction(USERS_DB)) {
        $money_withdrawed = query_multiple_params(USERS_DB,
            "UPDATE vk_task.users AS vk_user
            SET vk_user.balance = vk_user.balance - ?
            WHERE vk_user.id = ? AND vk_user.balance >= ?",
            'iii', $amount, $employer_id, $amount);

        if ($money_withdrawed) {
            $order_created = query_multiple_params(USERS_DB,
                "INSERT INTO vk_task.orders (title, reward, employer_id, employer_name) 
            VALUES (?,?,?,?)",
                'siis', $title, $amount, $employer_id, $employer_name);
            if ($order_created) {
                end_transaction(USERS_DB);
                return true;
            }
        }

        rollback_transaction(USERS_DB);
    }
    return false;
}