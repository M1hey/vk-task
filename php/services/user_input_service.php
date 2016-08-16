<?php

function check_str($str) {
    return htmlspecialchars(strip_tags($str), ENT_QUOTES);
}

function check_float($num) {
    return floatval(htmlspecialchars($num, ENT_QUOTES));
}

function check_uint($num) {
    $int_val = intval(htmlspecialchars($num, ENT_QUOTES));
    if (!$int_val || $int_val <= 0) {
        return false;
    }
    return $int_val;
}

function check_str_trim($str) {
    return trim(check_str($str));
}