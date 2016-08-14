<?php

require_once dirname(__DIR__) . '/db.php';

define('USER_TYPE_WORKER', 1);
define('USER_TYPE_EMPLOYER', 2);

define('PWD_HASH_COST', 12);

function get_user_by_id($id) {
    return single_result(query(USERS_DB_SLAVE,
        "SELECT id, username, account_type, balance FROM users WHERE id=?", 'i', $id));
}

function login_user($login, $pwd) {
    $q = "SELECT id, login, username, password_hash, account_type, balance FROM users WHERE login=?";

    $user = single_result(query(USERS_DB_SLAVE, $q, 's', $login));
    if ($user) {
        // TODO move this operation higher?
        if (password_verify($pwd, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }
    }

    return false;
}
