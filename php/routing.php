<?php

function include_full_page($name) {
    resolve_view_name($name);

    include dirname(__DIR__) . '/php/view/template_view.php';
}

function include_only_content($name) {
    include dirname(__DIR__) . '/php/view/' . resolve_view_name($name);
}

function resolve_view_name($name) {
    global $view;

    switch ($name) {
        case 'login':
            $view = 'login_view.php';
            break;
        case 'user':
            $view = 'user_view.php';
            break;
        default:
            $view = 'not_found_view.php';
            break;
    }

    return $view;
}