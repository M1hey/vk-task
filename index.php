<?php
require_once 'php/view/view_helper.php';
require_once 'php/services/session_service.php';

require_once 'php/controllers/login_controller.php';
require_once 'php/controllers/user_controller.php';
require_once 'php/controllers/order_controller.php';

session_check();

$routes = explode('/', $_SERVER['REQUEST_URI']);
header('accept-charset="UTF-8"');

$controller_path = $routes[1];
if ($controller_path == 'login' || isset($_GET['logout'])) {
    process_login();
} elseif ($controller_path == 'favicon.ico') {
    // ignored yet
} else {
    $user = get_logged_in_user();

    if ($user) {
        if ($controller_path == '' || $controller_path == 'user') {
            process_user($user);
        } elseif ($controller_path == 'add_order') {
            process_add_order($user);
        } else {
            include_full_page('not_found_view.php');
        }
    } else {
        include_full_page('login_view.php');
    }
}