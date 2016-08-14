<?php

require_once dirname(__DIR__) . '/view/view_helper.php';
require_once dirname(__DIR__) . '/services/session_service.php';
require_once dirname(__DIR__) . '/model/order.php';
require_once dirname(__DIR__) . '/model/system.php';

// todo refactor: it's more than controller now
function process_order_complete($user) {
    $order_id = validate_order_complete_input();

    $result = ['success' => false];
    if ($order_id) {
        $order_completed = complete_order($order_id, $user['id']);

        if ($order_completed) {
            $result = ['success' => true];

            $updated_user = get_user_by_id($user['id']);
            if ($updated_user) {
                $result['new_balance'] = $updated_user['balance'];
                $result['reward'] = $updated_user['balance'] - $user['balance'];
            }

            // get sys acc balance
            $system_balance = get_system_balance();
            if ($system_balance) {
                $result['system_balance'] = $system_balance['balance'];
            } else {
                $result['system_balance'] = false;
            }
        } else {
            // get new orders
            $result['msg'] = "Невозможно выполнить заказ. Его уже кто-то выполнил.";
        }
    }

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
    $input = validate_add_order_input($user['balance']);
    if ($input) {
        $title = $input['title'];
        $amount = $input['amount'];

        if (create_order($user['id'], $user['username'], $title, $amount)) {
            global $order_title, $order_amount, $acc_balance;
            $order_title = $title;
            $order_amount = $amount;
            $acc_balance = $user['balance'] - $amount;

            // return new order and balance
            include_only_content('order_view.php');
        } else {
            echo false;
        }
    } else {
        echo false;
        exit();
    }
}

function validate_add_order_input($user_balance) {
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