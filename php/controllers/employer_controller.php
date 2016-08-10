<?php

require_once dirname(__DIR__) . '/model/order.php';

function process_employer($user) {
    global $orders;

    $orders = get_orders_by_emp_id($user);
}