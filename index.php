<?php
define('ROOT', dirname(__DIR__) . '/php/');

require_once 'php/model/user/user.php';

$user = get_user($_GET['login'], $_GET['pwd']);

$account_name = $user['name'];
$acc_balance = $user['balance'];
$sys_balance = "100$";
require_once dirname(__DIR__) . '/vk-task/php/view/template_view.php';