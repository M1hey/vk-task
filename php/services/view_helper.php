<?php

function include_full_page($view_name) {
    global $view;
    $view = $view_name;

    include dirname(__DIR__) . '/view/template_view.html';
}

function include_only_content($name) {
    include dirname(__DIR__) . '/view/' . $name;
}

function include_inline($name){
    ob_start();
    include dirname(__DIR__) . '/view/' . $name;
    return ob_get_clean();
}