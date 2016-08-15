<?php

require_once dirname(__DIR__) . '/view/view_helper.html';
require_once dirname(__DIR__) . '/services/session_service.php';
require_once dirname(__DIR__) . '/services/money.php';
require_once dirname(__DIR__) . '/model/order.php';
require_once dirname(__DIR__) . '/model/system.php';

// todo refactor: it's more than controller now
function process_order_complete($user) {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        echo false;
    }
    $order_id = validate_order_complete_input();

    $result = [];
    if ($order_id) {
        $order_completed = complete_order($order_id, $user['id']);

        if ($order_completed) {
            $result = ['success' => true];

            $updated_user = get_user_by_id($user['id']);
            if ($updated_user) {
                $result['new_balance'] = format_money($updated_user['balance']);
                $result['reward'] = format_money($updated_user['balance'] - $user['balance']);
            }

            // get sys acc balance
            $result['system_balance'] = format_money(get_system_balance());
        } else {
            // get new orders
            $result['msg'] = "Невозможно выполнить заказ. Возможно, он уже выполнен.";
            $new_orders = '';

            $orders = get_orders();
            if ($orders && count($orders)) {
                global $order_id, $order_amount, $order_title, $order_employer;

                foreach ($orders as $order) {
                    $order_id = $order['id'];
                    $order_title = $order['title'];
                    $order_amount = $order['reward'];
                    $order_employer = $order['employer_name'];

                    $new_orders = $new_orders . include_inline('order_worker_view.html');
                }
                $result['new_orders'] = $new_orders;
            } else {
                $result['new_orders'] = false;
            }

        }
    } else {
        $result['success'] = false;
        $result['msg'] = "Неверный номер заказа";
    }

    header('Content-Type: application/json');
    echo json_encode($result);
}

function validate_order_complete_input() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' || !isset($_POST['order_id'])) {
        $order_id = intval(htmlspecialchars($_POST['order_id'], ENT_QUOTES));

        if (!$order_id || $order_id <= 0) {
//            show_order_error("Стоимость заказа заказа должна быть числом больше 0");
            return false;
        }

        return $order_id;
    }
    return false;
}

function process_add_order($user) {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        echo false;
    }

    $input = validate_add_order_input($user['balance']);
    $result = ['success' => false];

    if ($input) {
        $title = $input['title'];
        $amount = $input['amount'];

        if (create_order($user['id'], $user['username'], $title, $amount)) {
            global $order_title, $order_amount, $acc_balance;
            $order_title = $title;
            $order_amount = $amount;
            $acc_balance = $user['balance'] - $amount;


            // return new order and balance
            $result = [
                'success' => true,
                'balance' => format_money($acc_balance),
                'order_html' => include_inline('order_view.html')];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($result);
}

function validate_add_order_input($user_balance) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' || !isset($_POST['title']) || !isset($_POST['amount'])) {
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
        if (empty($title)) {
//            title must not be empty
            // todo HOW to return proper error message!!!!
            return false;
        }
        $amount = floatval(htmlspecialchars($_POST['amount'], ENT_QUOTES));
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
            'amount' => $amount * 100];
    }
    return false;
}