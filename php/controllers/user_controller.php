<?php

require_once dirname(__DIR__) . '/view/view_helper.php';

function process_user() {
    if (isset($_SESSION['logged_in'])) {
        include_full_page('user_view.php');
    } else {
        include_full_page('login_view.php');
    }
}