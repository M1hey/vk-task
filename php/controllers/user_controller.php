<?php

require_once dirname(__DIR__) . '/services/view_helper.php';
require_once dirname(__DIR__) . '/services/session_service.php';
require_once dirname(__DIR__) . '/services/money.php';
require_once dirname(__DIR__) . '/services/msg_service.php';
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

function process_update_user_balance_ajax($user) {
    $new_balance = get_updated_user_balance($user['id'], $user['balance']);

    json_respond_success(['new_balance' => format_money($new_balance)]);
}

function get_view_name_by_type($user) {
    switch ($user['account_type']) {
        case USER_TYPE_WORKER:
            return 'user_worker_view.php';
        case USER_TYPE_EMPLOYER:
            return 'user_employer_view.php';
        default:
            return 'not_found_view.php';
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