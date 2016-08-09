<?php

function process_login() {
    require_once dirname(__DIR__) . '/view/view_helper.php';
    require_once dirname(__DIR__) . '/controllers/session_controller.php';


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = login_user(htmlspecialchars($_POST['login'], ENT_QUOTES), htmlspecialchars($_POST['password'], ENT_QUOTES));

        if ($user) {
            if (create_auth_token_for_user($user['id'], $user['username'])) {
                require_once dirname(__DIR__) . '/controllers/user_controller.php';
                show_user_ajax($user);
            } else {
                echo false;
            }
        } else {
            echo false;
        }
    } else {
        include_full_page('login_view.php');
    }
}