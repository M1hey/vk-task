<?php
require_once 'php/services/view_helper.php';
require_once 'php/services/session_service.php';
require_once 'php/services/user_input_service.php';
require_once 'php/services/msg_service.php';

require_once 'php/controllers/login_controller.php';
require_once 'php/controllers/user_controller.php';
require_once 'php/controllers/order_controller.php';

//TODO disable connection without cookies
session_check();
set_headers();

$routes = str_replace('//', '/', $_SERVER['REQUEST_URI']);
$routes = explode('/', $routes);
$allowed_get_without_csrf = array('', 'login', 'user', '?logout');
$controller_path = check_str($routes[1]);

//TODO recover securely session if timed out
if (!csrf_check($controller_path, $allowed_get_without_csrf)) {
    if (isset($_SERVER['HTTP_X_AJAX'])) {
        json_respond_fail("Сессия устарела, обновите страницу");
    } else {
        include_full_page('not_found_view.php');
    }
    exit();
};

if ($controller_path == 'login' || isset($_GET['logout'])) {
    process_login();
} else {
    if ($controller_path == '' || $controller_path == 'user') {
        process_user(get_user_or_go_to_login());
    } elseif ($controller_path == 'add_order') {
        process_add_order(get_user_or_go_to_login());
    } elseif ($controller_path == 'complete_order') {
        process_order_complete(get_user_or_go_to_login());
    } elseif ($controller_path == 'load_more_orders') {
        process_load_more_orders();
    } elseif ($controller_path == 'update_acc_balance') {
        process_update_user_balance_ajax(get_user_or_say_to_refresh_page());
    } else {
        include_full_page('not_found_view.php');
    }
}

// TODO it fails on ajax
function get_user_or_go_to_login() {
    $user = get_logged_in_user();
    if ($user) {
        return $user;
    } else {
        include_full_page('login_view.php');
        exit();
    }
}

function get_user_or_say_to_refresh_page() {
    $user = get_logged_in_user();
    if ($user) {
        return $user;
    } else {
        json_respond_fail("Таймаут сессии, обновите страницу");
        exit();
    }
}