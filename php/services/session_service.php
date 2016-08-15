<?php

require_once dirname(__DIR__) . '/model/session.php';
require_once dirname(__DIR__) . '/services/security_service.php';

/*prevent session fixation*/
function session_check() {
    set_session_params();
    session_start();

    if (isset($_GET['logout']) ||
        (isset($_SESSION['PREV_REMOTEADDR']) && $_SERVER['REMOTE_ADDR'] !== $_SESSION['PREV_REMOTEADDR']) ||
        (isset($_SESSION['PREV_USERAGENT']) && $_SERVER['HTTP_USER_AGENT'] !== $_SESSION['PREV_USERAGENT'])
    ) {
        session_clear();
    }

    session_regenerate_id(true); // Generate a new session identifier

    $_SESSION['PREV_USERAGENT'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['PREV_REMOTEADDR'] = $_SERVER['REMOTE_ADDR'];
}

function create_auth_token_for_user($user_id, $username) {
    $new_token = generate_token(64);
    if (create_auth_token($user_id, $new_token, hash("sha256", $username))) {
        $_SESSION['auth_token'] = $new_token;
        $_SESSION['username'] = $username;
        return true;
    } else {
        error_log("Can't login user: \"" . $_POST['login'] . "\" because can't generate new token");
        return false;
    }
}

function get_logged_in_user() {
    if (isset($_SESSION['auth_token'])) {
        $auth_token = htmlspecialchars($_SESSION['auth_token'], ENT_QUOTES);
        $selector_string = htmlspecialchars($_SESSION['username'], ENT_QUOTES);

        $auth_token = get_auth_token($auth_token);
        if ($auth_token) {
            if (hash_equals(hash("sha256", $selector_string), $auth_token['validator_hash'])) {
                $new_token = generate_token(64);

                if (!update_auth_token($auth_token['id'], $new_token)) {
//                    $new_token = $auth_token; // TODO what should we do on fail generating new token?
                    error_log("Can't update auth_token for user: " . $auth_token['user_id']);
                    session_clear();
                    return false;
                }

                $user = get_user_by_id($auth_token['user_id']);
                $_SESSION['auth_token'] = $new_token;
                return $user;
            } else {
                return false;
            }
        } else {
            session_unset();
            session_destroy();
            return false;
        }
    }
    return false;
}

function session_clear() {
    session_unset();
    session_destroy();
}