<?php

require_once dirname(__DIR__) . '/db.php';

define('USER_TYPE_WORKER', 1);
define('USER_TYPE_EMPLOYER', 2);

define('PWD_HASH_COST', 12);

function get_user_by_id($id) {
    $q = "SELECT id, username, account_type, balance FROM users WHERE id=?";

    $result = query(USERS_DB_SLAVE, $q, 'i', $id);
    if($result) {
        return $result[0];
    } else {
        return false;
    }
}

function login_user($login, $pwd) {
    $q = "SELECT id, login, username, password_hash, account_type, balance FROM users WHERE login=?";

    $user = query(USERS_DB_SLAVE, $q, 's', $login);
    if ($user) {
        $user = $user[0]; // TODO move this operation higher?
        if (password_verify($pwd, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }
    }

    return false;
}
