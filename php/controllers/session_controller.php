<?php

require_once dirname(__DIR__) . '/model/session.php';

/*prevent session fixation*/
function session_check() {
    session_start();

    if (isset($_GET['logout']) ||
        (isset($_SESSION['PREV_REMOTEADDR']) && $_SERVER['REMOTE_ADDR'] !== $_SESSION['PREV_REMOTEADDR']) ||
        (isset($_SESSION['PREV_USERAGENT']) && $_SERVER['HTTP_USER_AGENT'] !== $_SESSION['PREV_USERAGENT'])
    ) {
        session_unset();
        session_destroy();
    }

    session_regenerate_id(); // Generate a new session identifier

    $_SESSION['PREV_USERAGENT'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['PREV_REMOTEADDR'] = $_SERVER['REMOTE_ADDR'];
}

function create_auth_token_for_user($user_id, $username) {
    $new_token = generate_token(64);
    if (create_auth_token($user_id, $new_token, hash("sha256", $username))) {
        $_SESSION['auth_token'] = $new_token;
        return true;
    } else {
        error_log("Can't login user: \"" . $_POST['login'] . "\" because can't generate new token");
        return false;
    }
}

function get_logged_in_user() {
    if (isset($_SESSION['auth_token'])) {
        $token = $_SESSION['auth_token'];
        $selector_string = $_SESSION['username'];

        $token = get_auth_token(hash("sha256", $selector_string), $token);
        if ($token) {
            $new_token = generate_token(64);

            if (!update_auth_token($token['token_id'], $new_token)) {
                $new_token = $token; // todo
                error_log("Can't update token for user: " . $token['user_id']);
            }

            $user = get_user_by_id($token['user_id']);
            $_SESSION['auth_token'] = $new_token;
            return $user;
        } else {
            session_unset();
            session_destroy();
            return false;
        }
    }
    return false;
}