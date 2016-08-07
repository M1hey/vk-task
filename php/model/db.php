<?php

define('USERS_DB', 'users_db');
define('SESSIONS_DB', 'sessions_db');

$database_connections = [];
$database_initialized = false;

function query($db, $query_statement, $types, &$vars)
{
    global $database_connections, $database_initialized;

    if (!$database_initialized) {
        // TODO: how to execute it once?
        init();
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

function init()
{
    global $users_db, $database_connections, $database_initialized;
    require_once dirname(__DIR__) . '/config/db_config.php';

    $database_connections[USERS_DB] = create_connection($users_db);
    $database_connections[SESSIONS_DB] = $database_connections[USERS_DB];
    $database_initialized = true;
}

function create_connection($db_opts)
{
    $connection = mysqli_connect($db_opts[DB_HOST], $db_opts[DB_USER], $db_opts[DB_PASSWORD], $db_opts[DB_DATABASE]);
    mysqli_set_charset($connection, "utf8");

    if (mysqli_connect_errno()) {
        // usually it will be error_log(msg, 1, admin@email.ru);
        error_log("Can't connect: " . mysqli_connect_error(), 0);
        exit();
    }

    return $connection;
}