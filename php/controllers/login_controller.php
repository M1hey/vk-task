<?php
require_once dirname(__DIR__) . '/services/view_helper.php';
require_once dirname(__DIR__) . '/services/session_service.php';
require_once dirname(__DIR__) . '/services/user_input_service.php';
require_once dirname(__DIR__) . '/controllers/user_controller.php';

function process_login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = get_user_by_login(check_str_trim($_POST['login']));

        if ($user) {
            $password = check_str_trim($_POST['password']);

            if (password_verify($password, $user['password_hash'])) {
                unset($user['password_hash']);
            } else {
                echo false;
            }

            if (create_auth_token_for_user($user['id'])) {
                show_user_ajax($user);
            } else {
                echo false;
            }
        } else {
            echo false;
        }
    } else {
        include_full_page('login_view.html');
    }
}