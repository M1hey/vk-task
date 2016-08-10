<?php

require_once dirname(__DIR__) . '/model/order.php';

function process_worker($user) {
    global $orders, $account_name, $acc_balance, $sys_balance;

    $account_name = $user['username'];
    $acc_balance = $user['balance'];
    $sys_balance = '100$';

    $orders = get_orders();
}