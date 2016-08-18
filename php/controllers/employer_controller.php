<?php

require_once dirname(__DIR__) . '/model/order.php';
require_once dirname(__DIR__) . '/model/system.php';

function process_employer($user) {
    global $orders, $account_name, $acc_balance, $sys_balance;

    $account_name = $user['username'];
    $acc_balance = $user['balance'];
    $sys_balance = get_system_balance();

    $orders = get_orders_by_emp_id($user['id']);
}