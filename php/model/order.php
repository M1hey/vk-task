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
    // order is no more accessible to other users
    // now we can do it advisedly
    $reward_to_user = $order['reward'] - $order['commission'];
    $reward_credited = add_credit_user_operation($order_id, $reward_to_user, $user_id);
    if (!$reward_credited || !increase_user_balance($user_id, $reward_to_user)) {
        return false;
    } else {
        // wo could make a response from here
    }

    $system_operation_added = add_system_credit_operation($order_id, $order['commission']);
    if (!$system_operation_added || !increase_system_balance($order_id, $order['commission'])) {
        return false;
    }

    return mark_order_completed($order_id);
}

function add_credit_user_operation($order_id, $reward_to_user, $worker_id) {
    $success = query_multiple_params(USERS_DB_MASTER,
        "INSERT INTO credit_operations (order_id, worker_id, reward_to_user) VALUES (?, ?, ?)",
        'iii', $order_id, $worker_id, $reward_to_user);

    if (!$success) {
        error_log("Can't add user operation by order# " . $order_id);
    }

    return $success;
}

function increase_user_balance($worker_id, $reward_to_user) {
    // add money to account
    $success = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users AS u 
            SET u.balance = u.balance + ?
          WHERE u.id = ?",
        'ii', $reward_to_user, $worker_id);

    if (!$success) {
        error_log("Can't add reward to user user#" . $worker_id);
    }

    return $success;
}

function add_system_credit_operation($order_id, $order_commission) {
    $success = query_multiple_params(SYSTEM_DB,
        "INSERT INTO system_credit_operations(order_id, commission) VALUES (?, ?)",
        'ii', $order_id, $order_commission);

    if (!$success) {
        error_log("Can't add system credit operation for order#" . $order_id);
    }

    return $success;
}

function increase_system_balance($order_id, $amount) {
    // add commission to system
    $success = query_multiple_params(USERS_DB_MASTER,
        "UPDATE system_account
            SET system_account.balance = system_account.balance + COALESCE(?, 0)
          WHERE system_account.id = 1", 'i', $amount);

    if (!$success) {
        error_log("Can't increase system balance (" . $amount . ") from order#" . $order_id);
    }

    return $success;
}

function mark_order_completed($order_id) {
    $success = query_multiple_params(ORDERS_DB_MASTER,
        "UPDATE orders AS o 
            SET o.status = 'completed'
          WHERE o.id = ? 
            AND o.status = 'reserved'", 'i', $order_id);

    if (!$success) {
        error_log("Can't mark order #" . $order_id . " completed");
    }

    return $success;
}

/*recovery function not present*/
function create_order($employer_id, $employer_name, $title, $amount) {
    $system_commission_percent = single_result(query(SYSTEM_DB, "SELECT commission_percent FROM system_account"));

    if (!$system_commission_percent) {
        return false;
    }
    $system_commission_percent = $system_commission_percent['commission_percent'];

    // optimistic order creation
    $system_commission = intval($amount * $system_commission_percent / 100);
    $order_id = query_multiple_params(ORDERS_DB_MASTER,
        "INSERT INTO orders (title, reward, employer_id, employer_name, commission) 
            VALUES (?,?,?,?,?)",
        'siisi', $title, $amount, $employer_id, $employer_name, $system_commission);

    if (!$order_id) {
        return false;
    }
    // create transaction
    $transaction_id = query_multiple_params(USERS_DB_MASTER,
        "INSERT INTO order_creation_transactions (employer_id, order_id, amount) VALUES (?,?,?)",
        'iii', $employer_id, $order_id, $amount);
    if (!$transaction_id) {
        return false;
    }

    //      withdraw money - order_paid,
    $money_withdrawed = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users AS u, order_creation_transactions AS t
            SET
              u.balance = u.balance - t.amount,
              t.status = 'order_paid'
            WHERE u.id = ? 
              AND t.id = ?
              AND u.balance >= t.amount",
        'ii', $employer_id, $transaction_id);

    if (!$money_withdrawed) {
        return false;
    }

    $order_paid = query(ORDERS_DB_MASTER,
        "UPDATE orders AS o SET o.status = 'paid' WHERE o.id = ?", 'i', $order_id);

    return $order_paid;
}

function get_orders_from($order_id) {
    return query(ORDERS_DB_SLAVE,
        "SELECT id, title, reward, employer_name 
           FROM orders 
          WHERE status = 'paid' 
            AND id > ? 
            LIMIT 3", 'i', $order_id);
}

function get_first_orders() {
    return query(ORDERS_DB_SLAVE,
        "SELECT id, title, reward, employer_name FROM orders WHERE status = 'paid' LIMIT 3");
}

function get_orders_by_emp_id($employer_id) {
    return query(ORDERS_DB_SLAVE,
        "SELECT id, title, reward FROM orders WHERE employer_id = ? AND status = 'paid'",
        'i', $employer_id);
}

function recover_order_completion_from_failure() {
    $orders = query(ORDERS_DB_SLAVE,
        "SELECT id, reward, commission, worker_id FROM orders WHERE status = 'reserved'");

    if ($orders && count($orders)) {
        error_log("----Started recovery for " . count($orders) . " orders");

        foreach ($orders as $order) {
            $order_id = $order['id'];
            $order_reward = $order['reward'];
            $order_commission = $order['commission'];
            $worker_id = $order['worker_id'];

            error_log("--Started recovery for order #" . $order_id);

            // check user transaction
            $user_transaction = single_result(query(USERS_DB_SLAVE,
                "SELECT id, order_id, worker_id, reward_to_user, status FROM users_transactions 
                        WHERE order_id = ?", 'i', $order_id));
            if (!$user_transaction) {
                $user_transaction_id = add_credit_user_operation($order_id, $order_reward, $order_commission, $order_reward);
                if (!($user_transaction_id && increase_user_balance($worker_id, $user_transaction_id))
                ) {
                    continue;
                }
            } elseif ($user_transaction['status'] != 'completed') {
                if (!increase_user_balance($worker_id, $user_transaction['id'])) {
                    continue;
                }
            }

            // check system transaction
            $system_transaction = single_result(query(SYSTEM_DB,
                "SELECT id, order_id, commission, status, status FROM system_transactions 
                                WHERE order_id = ?", 'i', $order_id));
            if (!$system_transaction) {
                $system_transaction_id = add_system_credit_operation($order_id, $order_commission);
                if (!($system_transaction_id && increase_system_balance($system_transaction_id, $order_id))
                ) {
                    continue;
                }
            } elseif ($system_transaction['status'] != 'completed') {
                if (!increase_system_balance($system_transaction['id'], $order_id)) {
                    continue;
                }
            }

            if (mark_order_completed($order_id)) {
                error_log("Order #" . $order_id . " completed");
            }
        }
    }
}