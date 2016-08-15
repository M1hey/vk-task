<?php
require_once dirname(__DIR__) . '/services/view_helper.php';
require_once dirname(__DIR__) . '/services/session_service.php';
require_once dirname(__DIR__) . '/controllers/user_controller.php';

function process_login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = get_user_by_login(trim(htmlspecialchars($_POST['login']), ENT_QUOTES));

        if ($user) {
            $password = trim(htmlspecialchars($_POST['password'], ENT_QUOTES));

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