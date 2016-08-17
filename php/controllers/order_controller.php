<?php

require_once dirname(__DIR__) . '/services/view_helper.php';
require_once dirname(__DIR__) . '/services/session_service.php';
require_once dirname(__DIR__) . '/services/money.php';
require_once dirname(__DIR__) . '/services/user_input_service.php';
require_once dirname(__DIR__) . '/model/order.php';
require_once dirname(__DIR__) . '/model/system.php';

// todo refactor: it's more than controller now
function process_order_complete($user) {
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
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
        return check_uint($_POST['order_id']);
    }

    return false;
}

function process_add_order($user) {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        die("Not allowed");
    }

    $input = validate_add_order_input($user['balance']);
    $result = ['success' => false];

    if ($input['success']) {
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
        } else {
            $result = [
                'success' => false,
                'msg' => "Не удалось добавить заказ. Попробуйте позже или измените входные данные."];
        }
    } else {
        $result = $input;
    }

    header('Content-Type: application/json');
    echo json_encode($result);
}

function validate_add_order_input($user_balance) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['amount'])) {
        $title = check_str($_POST['title']);
        if (empty($title)) {
            return ['success' => false,
                'msg' => 'Введите заголовок'];
        }
        $amount = check_float($_POST['amount']);
        if (empty($amount)) {
            return ['success' => false,
                'msg' => "Введите стоимость"];
        }
        if (!$amount || $amount <= 0) {
            return ['success' => false,
                'msg' => "Стоимость заказа заказа должна быть числом больше 0"];
        }
        if ($amount > $user_balance) {
            return ['success' => false,
                'msg' => "У вас недостаточно средств, чтобы разместить заказ"];
        }

        return ['success' => true,
            'title' => $title,
            'amount' => $amount * 100];
    }

    return ['success' => false, 'msg' => 'Неверный запрос'];
}