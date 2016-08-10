<?php

require_once dirname(__DIR__) . '/view/view_helper.php';
require_once dirname(__DIR__) . '/controllers/session_controller.php';
require_once dirname(__DIR__) . '/model/order.php';

function process_add_order($user) {
    $input = validate_input($user['balance']);
    if ($input) {
        $title = $input['title'];
        $amount = $input['amount'];

        if (create_order($user['id'], $user['username'], $title, $amount)) {
            global $order_title, $order_amount, $acc_balance;

            // return new order and balance
            include_only_content('order_created_view.php');
        } else {
            echo false;
        }
    } else {
        echo false;
        exit();
    }
}

function validate_input($user_balance) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' || !isset($_POST['title']) || !isset($_POST['amount'])) {
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
        if (empty($title)) {
//            title must not be empty
            // todo HOW to return proper error message!!!!
            return false;
        }
        $amount = intval(htmlspecialchars($_POST['amount'], ENT_QUOTES));
        if (empty($amount)) {
//            show_order_error("Введите стоимость");
            return false;
        }
        if (!$amount || $amount <= 0) {
//            show_order_error("Стоимость заказа заказа должна быть числом больше 0");
            return false;
        }
        if ($amount > $user_balance) {
//            show_order_error("У вас недостаточно средств, чтобы разместить заказ");
            return false;
        }

        return ['title' => $title,
            'amount' => $amount];
    }
    return false;
}