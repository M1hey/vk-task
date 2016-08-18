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

            $orders = get_first_orders();
            $result['new_orders'] = render_orders($orders);
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
                'order_html' => include_content_in_var('order_view.php')];
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
        $amount = round($amount, 2);
        if (!$amount || $amount < 1) {
            return ['success' => false,
                'msg' => "Стоимость заказа должна быть числом не меньше 1$"];
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

function process_load_more_orders() {
    $last_order_id = get_load_more_orders_validated_input();

    $result = ['success' => false];

    if ($last_order_id) {
        $orders = get_orders_from($last_order_id);
        $result = ['success' => true];
        $result['more_orders'] = render_orders($orders);
    } else {
        $result['msg'] = "Не удалось загрузить заказы.";
    }

    header('Content-Type: application/json');
    echo json_encode($result);
}

function render_orders($orders) {
    $orders_html_str = '';
    if ($orders && count($orders)) {
        global $order_id, $order_amount, $order_title, $order_employer;

        foreach ($orders as $order) {
            $order_id = $order['id'];
            $order_title = $order['title'];
            $order_amount = $order['reward'];
            $order_employer = $order['employer_name'];

            $orders_html_str = $orders_html_str . include_content_in_var('order_worker_view.php');
        }
        return $orders_html_str;
    } else {
        return false;
    }
}

function get_load_more_orders_validated_input() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['last_order_id'])) {
        return check_uint($_POST['last_order_id']);
    }

    return false;
}
