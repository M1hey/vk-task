<?php
require_once 'php/view/view_helper.php';
require_once 'php/controllers/session_controller.php';

session_check();

$routes = explode('/', $_SERVER['REQUEST_URI']);

$controller = $routes[1];
if ($controller == 'login' || isset($_GET['logout'])) {
    require_once 'php/controllers/login_controller.php';
    process_login();
} elseif ($controller == '' || $controller == 'user') {
    require_once 'php/controllers/user_controller.php';
    process_user();
} else {
    include_full_page('not_found_view.php');
}