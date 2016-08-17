<?php

define('CSRF', 'HTTP_X_CSRF');

function create_csrf_token() {
    global $csrf_token;
    $csrf_token = generate_token(64);

    $_SESSION[CSRF] = $csrf_token;
}

function check_csrf() {
    if (!isset($_SESSION[CSRF]) || !isset($_SERVER[CSRF])) {
        return false;
    }

    if (($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'GET')
        || $_SESSION[CSRF] != $_SERVER[CSRF]
    ) {
        die("HTTP/1.1 405 Method Not Allowed");
    }

    return true;
}