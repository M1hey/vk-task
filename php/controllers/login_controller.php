<?php
require_once dirname(__DIR__) . '/services/view_helper.php';
require_once dirname(__DIR__) . '/services/session_service.php';
require_once dirname(__DIR__) . '/services/user_input_service.php';
require_once dirname(__DIR__) . '/services/csrf.php';
require_once dirname(__DIR__) . '/controllers/user_controller.php';

function process_login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = check_str_trim($_POST['login']);
        $user = get_user_by_login($login);
        $result['success'] = false;

        if ($user) {
            $password = check_str_trim($_POST['password']);

            if (($user['failed_attempts'] >= 3) && ((time() - $user['last_attempt_timestamp']) < 60)) {
                $result['msg'] = "Пароль был введён неправильно больше трёх раз. Подождите минуту";
                increase_user_login_failed($login);
            } else {
                if (password_verify($password, $user['password_hash'])) {
                    // reset failed_attempts
                    unset($user['password_hash']);
                    if ($user['failed_attempts'] > 0) {
                        reset_user_failed_attempts($login);
                    }

                    if (create_auth_token_for_user($user['id'])) {
                        $result['success'] = true;
                        $result['html'] = get_user_view_ajax($user);
                    } else {
                        $result['msg'] = "Ошибка сервера. Попробуйте позднее";
                    }
                } else {
                    increase_user_login_failed($login);
                    $result['msg'] = "Неверный логин или пароль";
                }
            }
        } else {
            $result['msg'] = "Неверный логин или пароль";
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        include_full_page('login_view.html');
    }
}