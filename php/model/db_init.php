<?php

$database_connections = [];
$database_initialized = false;

require_once dirname(__DIR__) . '/config/db_config.php';

function db_check_init() {
    global $remote_db, $local_db, $database_connections, $database_initialized;

    if (!$database_initialized) {/*Mocking multi database functionality*/
        $db_params = 'vk-task.ru' == $_SERVER['HTTP_HOST'] ? $local_db : $remote_db;
        $database_connections[USERS_DB_SLAVE] = create_connection($db_params);
        $database_connections[USERS_DB_MASTER] = $database_connections[USERS_DB_SLAVE];
        $database_connections[AUTH_TOKEN_DB] = $database_connections[USERS_DB_SLAVE];
        $database_connections[ORDERS_DB_MASTER] = $database_connections[USERS_DB_SLAVE];
        $database_connections[ORDERS_DB_SLAVE] = $database_connections[USERS_DB_SLAVE];
        $database_connections[SYSTEM_DB] = $database_connections[USERS_DB_SLAVE];
        $database_connections[MEMCACHED] = $database_connections[USERS_DB_SLAVE];
        $database_initialized = true;
    }
}

function get_db_link_by_name($db_name) {
    global $database_connections;

    return $database_connections[$db_name];
}

function create_connection($db_opts) {
    $connection = mysqli_connect($db_opts[DB_HOST], $db_opts[DB_USER], $db_opts[DB_PASSWORD], $db_opts[DB_DATABASE]);
    mysqli_set_charset($connection, "utf8");

    if (mysqli_connect_errno()) {
        // usually it will be error_log(msg, 1, admin@email.ru);
        error_log("Can't connect: " . mysqli_connect_error(), 0);
        exit();
    }

    return $connection;
}