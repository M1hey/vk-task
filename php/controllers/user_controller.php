<?php

require_once dirname(__DIR__) . '/services/view_helper.php';
require_once dirname(__DIR__) . '/services/session_service.php';
require_once dirname(__DIR__) . '/services/money.php';
require_once dirname(__DIR__) . '/controllers/employer_controller.php';
require_once dirname(__DIR__) . '/controllers/worker_controller.php';

function process_user($user) {
    prepare_view($user);
    include_full_page(get_view_name_by_type($user));
}

function get_user_view_ajax($user) {
    prepare_view($user);
    return include_content_in_var(get_view_name_by_type($user));
}

function get_view_name_by_type($user) {
    switch ($user['account_type']) {
        case USER_TYPE_WORKER:
            return 'user_worker_view.html';
        case USER_TYPE_EMPLOYER:
            return 'user_employer_view.html';
        default:
            return 'not_found_view.html';
    }
}

function prepare_view($user) {
    // TODO we don't need common controller
    switch ($user['account_type']) {
        case USER_TYPE_WORKER:
            process_worker($user);
            break;
        case USER_TYPE_EMPLOYER:
            process_employer($user);
            break;
    }
}