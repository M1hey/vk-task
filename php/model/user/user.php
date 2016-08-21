<?php

require_once dirname(__DIR__) . '/db.php';

define('USER_TYPE_WORKER', 1);
define('USER_TYPE_EMPLOYER', 2);

define('PWD_HASH_COST', 12);

function get_user_by_id($id) {
    return single_result(query(USERS_DB_SLAVE,
        "SELECT id, username, account_type, balance 
           FROM users 
          WHERE id=?", 'i', $id));
}

function get_user_by_login($login) {
    return single_result(query(USERS_DB_SLAVE,
        "SELECT id, login, username, password_hash, account_type, balance, failed_attempts, last_attempt_timestamp
           FROM users
          WHERE login=?", 's', $login));
}

function increase_user_login_failed($login) {
    return query(USERS_DB_SLAVE,
        "UPDATE users
            SET failed_attempts = failed_attempts + 1,
                last_attempt_timestamp = UNIX_TIMESTAMP()
          WHERE login=?", 's', $login);
}

function get_updated_user_balance($user_id, $prev_balance) {
    $tryCount = 0;
    $success = false;
    $new_balance = $prev_balance;

    do {
        $user_credit_balance = single_result(query(USERS_DB_SLAVE,
            "SELECT SUM(reward_to_user) FROM credit_operations
            WHERE worker_id = ?", 'i', $user_id));

        if ($user_credit_balance) {
            $user_credit_balance = intval($user_credit_balance['SUM(reward_to_user)']);
        } else {
            $tryCount++;
            continue;
        }
        /*$user_debit_balance = single_result(query(USERS_DB_SLAVE,
            "SELECT SUM(reward_to_user) FROM credit_operations
                WHERE worker_id = ?", 'i', $user_id));*/

        if ($user_credit_balance != $prev_balance) {
            $success = query_multiple_params(USERS_DB_MASTER,
                "UPDATE users 
            SET balance = ?
          WHERE balance = ?", 'ii', $user_credit_balance, $prev_balance);

            if ($success) {
                $new_balance = $user_credit_balance;
            } else {
                $updated_balance = single_result(query(USERS_DB_MASTER,
                    "SELECT balance FROM users
                      WHERE id = ?", 'i', $user_id));

                if ($updated_balance) {
                    $new_balance = $updated_balance['balance'];
                    $success = true;
                }
            }
        } else {
            $success = true;
        }
    } while ($tryCount++ < 3 && !$success);

    return $new_balance;
}

function reset_user_failed_attempts($login) {
    return query(USERS_DB_SLAVE,
        "UPDATE users
            SET failed_attempts = 0
          WHERE login=?", 's', $login);
}