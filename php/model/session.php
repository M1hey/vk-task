<?php

require_once 'db.php';
require_once 'user/user.php';

function session_init() {
    if (!isset($_SESSION['PREV_USERAGENT'])) {
        $_SESSION['PREV_USERAGENT'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['PREV_REMOTEADDR'] = $_SERVER['REMOTE_ADDR'];
    }
}

function session_check() {
    session_init();

    if (isset($_GET['logout']) ||
        $_SERVER['REMOTE_ADDR'] !== $_SESSION['PREV_REMOTEADDR'] ||
        $_SERVER['HTTP_USER_AGENT'] !== $_SESSION['PREV_USERAGENT']
    ) {
        session_unset();
        session_destroy();
    }

    session_regenerate_id(); // Generate a new session identifier

    $_SESSION['PREV_USERAGENT'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['PREV_REMOTEADDR'] = $_SERVER['REMOTE_ADDR'];
}

function get_user_by_auth_token($validator_string, $token) {
    $user_id = session_check_auth_token($validator_string, $token);
    if ($user_id) {
        return get_user_by_id($user_id);
    }

    session_destroy();
    return false;
}

function session_check_auth_token($validator_string, $token) {
    $validator_hash = query(SESSIONS_DB,
        "SELECT validator_hash, user_id FROM vk_task.auth_tokens AS tokens
          WHERE tokens.token = ?",
        's', $token);

    if ($validator_hash && hash_equals(hash("sha256", $validator_string), $validator_hash[0])) {
        //TODO: validator_hash should be also generated
        //TODO: regenerate token
        return $validator_hash[1];
    }

    return false;
}

function generateToken($length = 20) {
    return bin2hex(random_bytes($length));
}