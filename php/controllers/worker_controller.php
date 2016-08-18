<?php

require_once dirname(__DIR__) . '/model/order.php';
require_once dirname(__DIR__) . '/model/system.php';

function process_worker($user) {
    global $orders, $account_name, $acc_balance, $sys_balance;

//    recover_order_completion_from_failure();

    $account_name = $user['username'];
    $acc_balance = $user['balance'];
    $sys_balance = get_system_balance();

    $orders = get_first_orders();
}