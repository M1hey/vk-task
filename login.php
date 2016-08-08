<?php

require_once dirname(__DIR__) . '/vk-task/php/model/session.php';

session_start();

//session_check();
//

//if ($user_logged_in) {
//    $user = get_user_by_auth_token($_GET['login'], $_GET['token_from_cookie']);
//}

$user = login_user(htmlspecialchars($_POST['login'], ENT_QUOTES), htmlspecialchars($_POST['password'], ENT_QUOTES));

if ($user) {
    $_SESSION['logged_in'] = true; // TODO set auth cookie
    $_SESSION['username'] = $user['name'];
    $_SESSION['balance'] = $user['balance'];
    $_SESSION['sys_balance'] = "100$";

    include dirname(__DIR__) . '/vk-task/php/view/user_view.php';
}