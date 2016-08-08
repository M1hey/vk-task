<?php
define('ROOT', dirname(__DIR__) . '/php/');

require_once 'php/model/session.php';

session_start();

session_check();
$user = get_user_by_auth_token($_GET['login'], $_GET['token_from_cookie']);

//$user = login_user($_GET['login'], $_GET['pwd']);

$account_name = $user['name'];
$acc_balance = $user['balance'];
$sys_balance = "100$";
require_once dirname(__DIR__) . '/vk-task/php/view/template_view.php';