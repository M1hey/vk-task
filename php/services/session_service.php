<?php

require_once dirname(__DIR__) . '/model/session.php';
require_once dirname(__DIR__) . '/model/connection_counter.php';
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

    check_requests_per_period();

    session_regenerate_id(true); // Generate a new session identifier

    $_SESSION['PREV_USERAGENT'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['PREV_REMOTEADDR'] = $_SERVER['REMOTE_ADDR'];
}

function check_requests_per_period() {
    $ip = get_ip();
    $connection_counter = get_connection_counter_by_ip($ip);
    if (!$connection_counter) {
        create_connection_counter($ip);
    } else {
        if ((time() - $connection_counter['last_access_timestamp']) > 5) {
            drop_connection_counter($ip);
        } else {
            if ($connection_counter['requests_count'] > 10) {
                die("Too many connections");
            }
            increment_connection_counter($ip);
        }
    }
}

function create_auth_token_for_user($user_id) {
    $new_token = generate_token(64);
    $selector = generate_token(64);
    if (create_auth_token($user_id, $new_token, hash("sha256", $selector))) {
        $_SESSION['auth_token'] = $new_token;
        $_SESSION['selector'] = $selector;
        return true;
    } else {
        error_log("Can't login user: \"" . htmlspecialchars($_POST['login'], ENT_QUOTES) . "\" because can't generate new token");
        return false;
    }
}

function get_logged_in_user() {
    if (isset($_SESSION['auth_token'])) {
        $auth_token = htmlspecialchars($_SESSION['auth_token'], ENT_QUOTES);
        $selector_string = htmlspecialchars($_SESSION['selector'], ENT_QUOTES);

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

function get_ip() {
    //Just get the headers if we can or else use the SERVER global
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
    } else {
        $headers = $_SERVER;
    }
    //Get the forwarded IP if it exists
    if (array_key_exists('X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $the_ip = $headers['X-Forwarded-For'];
    } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
    } else if ($_SERVER['REMOTE_ADDR'] == '::1') {
        $the_ip = '127.0.0.1';
    } else {
        $the_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
    return $the_ip;
}