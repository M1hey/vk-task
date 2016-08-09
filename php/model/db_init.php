<?php

$database_connections = [];
$database_initialized = false;

function db_init()
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