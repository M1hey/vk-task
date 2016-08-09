<?php

define('USERS_DB', 'users_db');
define('SESSIONS_DB', 'sessions_db');

function query($db, $query_statement, $types, &$vars) {
    global $database_connections, $database_initialized;

    if (!$database_initialized) {    // TODO: how to execute it once? http://php.net/manual/ru/mysqli.persistconns.php
        require_once 'db_init.php';
        db_init();
    }

    $stmt = mysqli_prepare($database_connections[$db], $query_statement);

    if (!mysqli_stmt_bind_param($stmt, $types, $vars)) return false;
    if (!mysqli_stmt_execute($stmt)) return false;
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) return false;
    $result_set = mysqli_fetch_array($result, MYSQLI_NUM);
    if (!$result_set) return false;
    mysqli_stmt_close($stmt);

    return $result_set;
}
