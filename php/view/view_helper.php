<?php

function include_full_page($view_name) {
    global $view;
    $view = $view_name;

    include dirname(__DIR__) . '/view/template_view.php';
}

function include_only_content($name) {
    include dirname(__DIR__) . '/view/' . $name;
}