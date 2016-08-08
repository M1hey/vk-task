<?php

require_once dirname(__DIR__) . '/vk-task/php/routing.php';
require_once dirname(__DIR__) . '/vk-task/php/model/session.php';


//session_check();
//

//if ($user_logged_in) {
//    $user = get_user_by_auth_token($_GET['login'], $_GET['token_from_cookie']);
//}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    $user = login_user(htmlspecialchars($_POST['login'], ENT_QUOTES), htmlspecialchars($_POST['password'], ENT_QUOTES));

    if ($user) {
        $_SESSION['logged_in'] = true; // TODO set auth cookie
        $_SESSION['username'] = $user['name'];
        $_SESSION['balance'] = $user['balance'];
        $_SESSION['sys_balance'] = "100$";

        include_only_content('user');
    } else {
        echo false;
    }
} else {
    include_full_page('login');
}