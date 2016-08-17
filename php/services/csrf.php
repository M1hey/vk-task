<?php

define('CSRF', 'HTTP_X_CSRF');

function create_csrf_token() {
    global $csrf_token;
    $csrf_token = generate_token(64);

    $_SESSION[CSRF] = $csrf_token;
}

function csrf_check($path, $allowed_get_without_csrf, $not_found_func) {
    if (!csrf_is_set()) {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (in_array($path, $allowed_get_without_csrf, true)) {
                create_csrf_token();
            } else {
                $not_found_func();
                exit();
            }
        } else {
            die("HTTP/1.1 405 Method Not Allowed");
        }
    }
}

function csrf_is_set() {
    if (!isset($_SESSION[CSRF]) || !isset($_SERVER[CSRF])) {
        return false;
    }

    if (($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'GET')
        || $_SESSION[CSRF] != $_SERVER[CSRF]
    ) {
        return false;
    }

    return true;
}