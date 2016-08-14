<?php
require_once 'php/view/view_helper.php';
require_once 'php/services/session_service.php';

require_once 'php/controllers/login_controller.php';
require_once 'php/controllers/user_controller.php';
require_once 'php/controllers/order_controller.php';

session_check();
set_headers();

$routes = explode('/', $_SERVER['REQUEST_URI']);

$controller_path = $routes[1];
if ($controller_path == 'login' || isset($_GET['logout'])) {
    process_login();
} else {
    $user = get_logged_in_user();

    if ($user) {
        if ($controller_path == '' || $controller_path == 'user') {
            process_user($user);
        } elseif ($controller_path == 'add_order') {
            process_add_order($user);
        } elseif ($controller_path == 'complete_order') {
            process_order_complete($user);
        } else {
            include_full_page('not_found_view.php');
        }
    } else {
        include_full_page('login_view.php');
    }
}