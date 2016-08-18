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

// todo error log transaction status
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

    return start_user_transaction($transaction_id, $order_id, $order_reward, $order_commission, $worker_id)
    && process_user_transaction($worker_id, $transaction_id)
    && start_system_transaction($transaction_id, $order_id, $order_commission)
    && process_system_transaction($transaction_id, $order_id)
    && mark_order_transaction_completed($order_id, $transaction_id);
}


function start_user_transaction($transaction_id, $order_id, $order_reward, $order_commission, $worker_id) {
    $success = query_multiple_params(USERS_DB_MASTER,
        "INSERT INTO users_transactions (id, order_id, worker_id, reward_to_user) VALUES (?, ?, ?, ?)",
        'iiii', $transaction_id, $order_id, $worker_id, $order_reward - $order_commission);

    if (!$success) {
        error_log("Can't start user transaction #" . $transaction_id . ", order#" . $order_id);
    }

    return $success;
}

function process_user_transaction($worker_id, $transaction_id) {
    // add money to account
    $success = query_multiple_params(USERS_DB_MASTER,
        "UPDATE users AS u, users_transactions AS t 
                SET 
                  u.balance = u.balance + COALESCE(t.reward_to_user, 0), 
                  t.status = 'completed'
                WHERE u.id = ? 
                  AND t.id = ? 
                  AND t.status = 'created'",
        'ii', $worker_id, $transaction_id);

    if (!$success) {
        error_log("Can't process user transaction #" . $transaction_id . ", user#" . $worker_id);
    }

    return $success;
}

function start_system_transaction($transaction_id, $order_id, $order_commission) {
    $success = query_multiple_params(SYSTEM_DB,
        "INSERT INTO system_transactions(id, order_id, commission) VALUES (?, ?, ?)",
        'iii', $transaction_id, $order_id, $order_commission);
    if (!$success) {
        error_log("Can't start system transaction #" . $transaction_id . ", order#" . $order_id);
    }

    return $success;
}

function process_system_transaction($transaction_id, $order_id) {
    // add commission to system
    $success = query_multiple_params(USERS_DB_MASTER,
        "UPDATE system_account, system_transactions AS t
                SET 
                  system_account.balance = system_account.balance + t.commission,
                  t.status = 'completed'
                WHERE t.id = ?  
                  AND t.order_id = ? 
                  AND t.status = 'created'
                  AND system_account.id = 1", 'ii', $transaction_id, $order_id);

    if (!$success) {
        error_log("Can't process system transaction #" . $transaction_id . ", order#" . $order_id);
    }

    return $success;
}

function mark_order_transaction_completed($order_id, $transaction_id) {
    $success = query_multiple_params(ORDERS_DB_MASTER,
        "UPDATE orders AS o, orders_transactions AS t 
                SET 
                    o.status = 'completed',
                    t.status = 'completed'
                WHERE o.id = ? 
                  AND o.status = 'reserved'
                  AND t.id = ?", 'ii', $order_id, $transaction_id);

    if (!$success) {
        error_log("Can't mark order #" . $order_id . " transaction #" . $transaction_id . " finished");
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

            // check order transaction
            $order_transaction = single_result(query(ORDERS_DB_SLAVE,
                "SELECT id, worker_id, reward, commission, status FROM orders_transactions 
                    WHERE order_id = ?", 'i', $order_id));
            // assume that $order_transaction['status'] != 'completed' because order.status != 'completed'
            if (!$order_transaction) {
                if (!start_order_transaction($order_id, $order_reward, $order_commission, $worker_id)) {
                    error_log('Can\'t recover order #' . $order_id . ' from start transaction.');
                } else {
                    error_log("Order #" . $order_id . " completed");
                }
                continue;
            }

            // check if user transaction present
            $transaction_id = $order_transaction['id'];
            // check user transaction
            $user_transaction = single_result(query(USERS_DB_SLAVE,
                "SELECT id, order_id, worker_id, reward, status FROM orders_transactions 
                        WHERE id = ?", 'i', $transaction_id));
            if (!$user_transaction) {
                if (!(start_user_transaction($transaction_id, $order_id, $order_reward, $order_commission, $order_reward)
                    && process_user_transaction($worker_id, $transaction_id))
                ) {
                    continue;
                }
            } elseif ($user_transaction['status'] != 'completed') {
                if (!process_user_transaction($worker_id, $transaction_id)) {
                    continue;
                }
            }

            // check system transaction
            $system_transaction = single_result(query(SYSTEM_DB,
                "SELECT id, order_id, commission, status, status FROM system_transactions 
                                WHERE id = ?", 'i', $transaction_id));
            if (!$system_transaction) {
                if (!(start_system_transaction($transaction_id, $order_id, $order_commission)
                    && process_system_transaction($transaction_id, $order_id))
                ) {
                    continue;
                }
            } elseif ($system_transaction['status'] != 'completed') {
                if (!process_system_transaction($transaction_id, $order_id)) {
                    continue;
                }
            }

            if (mark_order_transaction_completed($order_id, $transaction_id)) {
                error_log("Order #" . $order_id . " completed");
            }
        }
    }
}