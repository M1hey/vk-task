<?php

require_once dirname(__DIR__) . '/view/view_helper.php';
require_once dirname(__DIR__) . '/controllers/session_controller.php';

function process_user($user) {
    set_user_view_vars($user);

    include_full_page(get_user_view_name_by_type($user['account_type']));
}


function set_user_view_vars($user) {
    global $account_name, $acc_balance, $sys_balance;

    $account_name = $user['username'];
    $acc_balance = $user['balance'];
    $sys_balance = '100$';
}

function show_user_ajax($user) {
    set_user_view_vars($user);
    include_only_content(get_user_view_name_by_type($user['account_type']));
}

function get_user_view_name_by_type($type) {
    switch ($type) {
        case USER_TYPE_WORKER:
            return 'user_view.php';
        case USER_TYPE_EMPLOYER:
            return 'user_employer_view.php';
        default:
            return 'not_found_view.php';
    }
}