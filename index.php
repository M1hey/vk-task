<?php
require_once 'php/routing.php';
require_once 'php/model/session.php';

session_start();

session_check();

//if ($user_logged_in) {
//    $user = get_user_by_auth_token($_GET['login'], $_GET['token_from_cookie']);
//}

if (isset($_SESSION['logged_in'])) {
    include_full_page('user');
} else {
    include_full_page('login');
}