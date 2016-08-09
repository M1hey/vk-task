<?php

require_once dirname(__DIR__) . '/view/view_helper.php';
require_once dirname(__DIR__) . '/controllers/session_controller.php';

function process_user() {
    $user = get_logged_in_user();

    if ($user) {
        set_user_view_vars($user);

        include_full_page('user_view.php');
    } else {
        include_full_page('login_view.php');
    }
}


function set_user_view_vars($user) {
    global $account_name, $acc_balance, $sys_balance;

    $account_name = $user['username'];
    $acc_balance = $user['balance'];
    $sys_balance = '100$';
}

function show_user_ajax($user) {
    set_user_view_vars($user);
    include_only_content('user_view.php');
}