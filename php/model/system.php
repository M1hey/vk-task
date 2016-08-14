<?php

require_once 'db.php';

function get_system_balance() {
    $result = single_result(query(USERS_DB_SLAVE, "SELECT balance FROM system_account WHERE id = 1"));
    if ($result) {
        return $result['balance'];
    } else {
        return false;
    }
}