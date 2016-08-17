<?php
require_once 'php/services/view_helper.php';
require_once 'php/services/session_service.php';
require_once 'php/services/user_input_service.php';

require_once 'php/controllers/login_controller.php';
require_once 'php/controllers/user_controller.php';
require_once 'php/controllers/order_controller.php';

//TODO disable connection without cookies
session_check();
set_headers();

$routes = explode('/', $_SERVER['REQUEST_URI']);

$allowed_get_without_csrf = array('login', 'user', '?logout');

$controller_path = check_str($routes[1]);

if (!check_csrf()) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (in_array($controller_path, $allowed_get_without_csrf, true)) {
            create_csrf_token();
        } else {
            include_full_page('not_found_view.html');
            exit();
        }
    } else {
        die("HTTP/1.1 405 Method Not Allowed");
    }
}

if ($controller_path == 'login' || isset($_GET['logout'])) {
    process_login();
} else {
    if ($controller_path == '' || $controller_path == 'user') {
        process_user(get_user_or_go_to_login());
    } elseif ($controller_path == 'add_order') {
        process_add_order(get_user_or_go_to_login());
    } elseif ($controller_path == 'complete_order') {
        process_order_complete(get_user_or_go_to_login());
    } else {
        include_full_page('not_found_view.html');
    }
}

// TODO it fails on ajax
function get_user_or_go_to_login() {
    $user = get_logged_in_user();
    if ($user) {
        return $user;
    } else {
        include_full_page('login_view.html');
        exit();
    }
}