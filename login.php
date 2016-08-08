<?php

require_once dirname(__DIR__) . '/vk-task/php/model/session.php';

session_start();

//session_check();
//

//if ($user_logged_in) {
//    $user = get_user_by_auth_token($_GET['login'], $_GET['token_from_cookie']);
//}

$user = login_user($_POST['login'], $_POST['password']);

if ($user) {
    $_SESSION['logged_in'] = true; // TODO set auth cookie
    $_SESSION['username'] = $user['name'];
    $_SESSION['balance'] = $user['balance'];
    $_SESSION['sys_balance'] = "100$";

    //Redirect the user.
    header('Location: /');
    exit;
}