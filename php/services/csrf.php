<?php

define('CSRF', 'HTTP_X_CSRF');

function create_csrf_token() {
    global $csrf_token;
    $csrf_token = generate_token(64);

    $_SESSION[CSRF] = $csrf_token;
}

function csrf_check($path, $allowed_get_without_csrf) {
    if (!csrf_is_set_correctly()) {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (in_array($path, $allowed_get_without_csrf, true)) {
                // allow only particular paths
                create_csrf_token();
            } else {
                return false;
            }
        } else {
            // don't allow POST without proper csrf token
            die("HTTP/1.1 405 Method Not Allowed");
        }
    }

    return true;
}

function csrf_is_set_correctly() {
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