<?php

require_once 'db.php';

function get_system_balance() {
    return single_result(query(USERS_DB_SLAVE, "SELECT balance FROM system_account WHERE id = 1"));
}