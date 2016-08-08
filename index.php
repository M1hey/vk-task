<?php
define('ROOT', dirname(__DIR__) . '/php/');

require_once 'php/model/session.php';

session_start();

session_check();

//if ($user_logged_in) {
//    $user = get_user_by_auth_token($_GET['login'], $_GET['token_from_cookie']);
//}
$view = ""; // TODO
if (isset($_SESSION['logged_in'])) {
    $account_name = $_SESSION['username'];
    $acc_balance = $_SESSION['balance'];
    $sys_balance = $_SESSION['sys_balance'];
    $view = 'user_view.php';
} else {
    $view = 'login_view.php';
}
include dirname(__DIR__) . '/vk-task/php/view/template_view.php';