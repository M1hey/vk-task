<?php

function format_money($amount) {
    return number_format($amount / 100, 2, '.', '');
}