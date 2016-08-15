<?php
require_once 'php/view/view_helper.php';
require_once 'php/services/session_service.php';

require_once 'php/controllers/login_controller.php';
require_once 'php/controllers/user_controller.php';
require_once 'php/controllers/order_controller.php';

session_check();
set_headers();

$routes = explode('/', $_SERVER['REQUEST_URI']);

$controller_path = htmlspecialchars($routes[1], ENT_QUOTES);
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
        include_full_page('not_found_view.php');
    }
}

function get_user_or_go_to_login() {
    $user = get_logged_in_user();
    if ($user) {
        return $user;
    } else {
        include_full_page('login_view.php');
        exit();
    }
}