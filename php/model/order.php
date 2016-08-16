<?php

require_once 'db.php';

function complete_order($order_id, $user_id) {
    $order_reserved = query_multiple_params(ORDERS_DB_MASTER,
        "UPDATE orders
            SET worker_id = ?, status = 'reserved' 
            WHERE id = ? 
              AND status = 'paid'
              AND orders.employer_id != ?",
        'iii', $user_id, $order_id, $user_id);

    if (!$order_reserved) {
        return false;
    }

    $order = single_result(query_multiple_params(ORDERS_DB_MASTER,
        "SELECT reward, commission FROM orders WHERE id = ? AND worker_id = ?", 'ii', $order_id, $user_id));

    if (!$order) {
        return false;
    }

    // everything correct
    return start_order_transaction($order_id, $order['reward'], $order['commission'], $user_id);
}

function start_order_transaction($order_id, $order_reward, $order_commission, $worker_id) {
    // order is no more accessible to other users
    // now we can do it advisedly
    // create transaction
    $transaction_id = query_multiple_params(ORDERS_DB_MASTER,
        "INSERT INTO orders_transactions (order_id, worker_id, reward, commission) VALUES (?, ?, ?, ?)",
        'iiii', $order_id, $worker_id, $order_reward, $order_commission);

    if (!$transaction_id) {
        return false;
    }

    if (start_user_transaction($transaction_id, $order_id, $order_reward, $order_commission, $worker_id)
        && start_system_transaction($transaction_id, $order_id, $order_commission)
    ) {
        $complete_transaction = query_multiple_params(ORDERS_DB_MASTER,
            "UPDATE orders AS o, orders_transactions AS t 
                SET 
                    o.status = 'completed',
                    t.status = 'completed'
                WHERE o.id = ? 
                  AND o.status = 'reserved'
                  AND t.id = ?", 'ii', $order_id, $transaction_id);

        return $complete_transaction;
    }

    return false;
}

function start_user_transaction($order_transaction_id, $order_id, $order_reward, $order_commission, $worker_id) {
    $users_transaction_created = query_multiple_params(USERS_DB_MASTER,
        "INSERT INTO users_transactions (id, order_id, worker_id, reward_to_user) VALUES (?, ?, ?, ?)",
        'iiii', $order_transaction_id, $order_id, $worker_id, $order_reward - $order_commission);

    if (!$users_transaction_created) {
        return false;
    }

    return process_user_transaction($worker_id, $order_transaction_id);
}

function process_user_transaction($worker_id, $transaction_id) {
    // add money to account
    $reward_credited = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users AS u, users_transactions AS t 
                SET 
                  u.balance = u.balance + COALESCE(t.reward_to_user, 0), 
                  t.status = 'completed'
                WHERE u.id = ? 
                  AND t.id = ? 
                  AND t.status = 'created'",
        'ii', $worker_id, $transaction_id);

    if (!$reward_credited) {
        return false;
    }

    return true;
}

function start_system_transaction($transaction_id, $order_id, $order_commission) {
    $system_transaction_created = query_multiple_params(SYSTEM_DB,
        "INSERT INTO system_transactions(id, order_id, commission) VALUES (?, ?, ?)",
        'iii', $transaction_id, $order_id, $order_commission);

    if (!$system_transaction_created) {
        return false;
    }
    return process_system_transaction($transaction_id, $order_id);
}

function process_system_transaction($transaction_id, $order_id) {
    // add commission to system
    $order_completed = query_multiple_params(USERS_DB_MASTER,
        "UPDATE system_account, system_transactions AS t
                SET 
                  system_account.balance = system_account.balance + t.commission,
                  t.status = 'completed'
                WHERE t.id = ?  
                  AND t.order_id = ? 
                  AND t.status = 'created'
                  AND system_account.id = 1", 'ii', $transaction_id, $order_id);

    if (!$order_completed) {
        return false;
    }

    return true;
}

/*TODO it should be transactional as well*/
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

    $system_commission = intval($amount * $system_commission_percent / 100);

    $order_id = query_multiple_params(USERS_DB_MASTER,
        "INSERT INTO orders (title, reward, employer_id, employer_name, commission) 
            VALUES (?,?,?,?,?)",
        'siisi', $title, $amount, $employer_id, $employer_name, $system_commission);

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
    return query(ORDERS_DB_SLAVE,
        "SELECT id, title, reward, employer_name FROM orders WHERE status = 'paid' LIMIT 15");
}

function get_orders_by_emp_id($employer_id) {
    return query(ORDERS_DB_SLAVE,
        "SELECT id, title, reward FROM orders WHERE employer_id = ? AND status = 'paid'",
        'i', $employer_id);
}

function recover_order_completion_from_failure() {
    $orders = query(ORDERS_DB_SLAVE,
        "SELECT id, title, reward, commission, worker_id FROM orders WHERE status = 'reserved'");

    foreach ($orders as $order) {
        // check order transaction
        $order_transaction = single_result(query(ORDERS_DB_SLAVE,
            "SELECT id, worker_id, reward, commission, status FROM orders_transactions 
                WHERE order_id = ?", 'i', $order['id']));
        if ($order_transaction) {
            // check user transaction
            $user_transaction = single_result(query(USERS_DB_SLAVE,
                "SELECT id, order_id, worker_id, reward, status FROM orders_transactions 
                    WHERE id = ?", 'i', $order_transaction));
            if ($user_transaction) {
                if ($user_transaction['status'] == 'completed') {
                    // check system transaction
                    $system_transaction = single_result(query(SYSTEM_DB,
                        "SELECT id, order_id, worker_id, reward, status FROM orders_transactions 
                            WHERE id = ?", 'i', $order_transaction));
                } else {
                    // TODO recheck everything
//                    process_user_transaction($user_transaction['worker_id'], $order_transaction['id']);
//                    start_system_transaction($u);
                }
            } else {
                start_user_transaction($order_transaction['id'], $order, $order['worker_id']);
            }
        } else {
            start_order_transaction($order, $order['worker_id']);
        }
    }
}