<?php

require_once dirname(__DIR__) . '/view/view_helper.php';
require_once dirname(__DIR__) . '/controllers/session_controller.php';

function process_user() {
    $user = get_logged_in_user();

    if ($user) {
        include_full_page('user_view.php');
    } else {
        include_full_page('login_view.php');
    }
}