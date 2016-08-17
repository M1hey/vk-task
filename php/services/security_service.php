<?php

// TODO: disable TRACE and OPTIONS
// SET-COOKIE SameSite=strict or do it in php ini
// Cookie validate id

function set_session_params() {
    session_name("vk_task");
    $domain = $_SERVER['SERVER_NAME'] == 'vk-task' ? 'vk-task' : 'o911998h.bget.ru';
    // secure == false since we don't have https set on test env
    session_set_cookie_params(300, '/', $domain, false, true);
}

function set_headers() {
    header('accept-charset="UTF-8"');
    header('Content-Type: text/html');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
}