<?php

define('USERS_DB_SLAVE', 'users_db');
define('USERS_DB_MASTER', 'users_db');
define('AUTH_TOKEN_DB', 'sessions_db');

require_once 'db_init.php';

function execute_in_transaction($db, $query_func) {
    global $database_connections;
    $link = $database_connections[$db];

    if (begin_transaction($db)) {
        $result = $query_func();

        if ($result) {
            end_transaction($db);
        } else {
            error_log("Transaction failed: [" . mysqli_errno($link) . "] " . mysqli_error($link));
            rollback_transaction($db);
        }

        return $result;
    } else {
        return false;
    }
}

function query_multiple_params($db, $query_statement, $types, ... $params) {
    global $database_connections, $database_initialized;

    /*Prepare params*/
    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
    $converted_params = array();

    $param_type = '';
    $n = count($params);

    for ($i = 0; $i < $n; $i++) {
        /* with call_user_func_array, array params must be passed by reference */
        $converted_params[$i] = &$params[$i];
    }

    /* Prepare statement */
    if (!$database_initialized) {    // TODO: how to execute it once? http://php.net/manual/ru/mysqli.persistconns.php
        db_init();
    }

    $link = $database_connections[$db];

    $stmt = mysqli_prepare($link, $query_statement);

    return handleQuery($link, function () use ($stmt, $types, $converted_params) {
        if (!$stmt) return false;

        /* use call_user_func_array, as $stmt->bind_param('s', $param); does not accept params array */
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt, $types), $converted_params));

        /* Execute statement */
        if (!mysqli_stmt_execute($stmt)) return false;

        /* Fetch result to array */
        $result = mysqli_stmt_get_result($stmt);
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        if ($result) {
            //TODO where to place mysqli_free_result
            $result_set = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if (!$result_set) return false;
            mysqli_stmt_close($stmt);

            return $result_set;
        } elseif ($affected_rows) {
            $insert_id = mysqli_stmt_insert_id($stmt);
            if ($insert_id) {
                return $insert_id;
            } else {
                return $affected_rows;
            }
        } else {
            return false;
        }

    }, $query_statement, $params);
}

// TODO: check fails
function query($db, $query_statement, $types = '', $param = null) {
    global $database_connections, $database_initialized;

    if (!$database_initialized) {    // TODO: how to execute it once? http://php.net/manual/ru/mysqli.persistconns.php
        db_init();
    }

    $link = $database_connections[$db];

    $stmt = mysqli_prepare($link, $query_statement);

    return handleQuery($link, function () use ($stmt, $types, $param) {
        if (!$stmt) return false;
        if ($param) {
            if (!mysqli_stmt_bind_param($stmt, $types, $param)) return false;
        }
        if (!mysqli_stmt_execute($stmt)) return false;
        $result = mysqli_stmt_get_result($stmt);
        if (!$result) return false;
        $result_set = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if (!$result_set) return false;
        mysqli_stmt_close($stmt);

        return $result_set;
    }, $query_statement, $param);
}

function handleQuery($link, $query_func, $query, $params) {
    $result = $query_func();
    if (!$result) {
        if (mysqli_errno($link)) {
            error_log("Can't execute query: " . mysqli_errno($link) . ': ' . mysqli_error($link) . "\n"
                . $query . "\n"
                . get_var_dump($params));
        }
    }

    return $result;
}

function get_var_dump($params) {
    ob_start();
    print_r($params);
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

// kind of private

function begin_transaction($db) {
    global $database_connections, $database_initialized;
    /* Prepare statement */
    if (!$database_initialized) {    // TODO: how to execute it once? http://php.net/manual/ru/mysqli.persistconns.php
        db_init();
    }

    $link = $database_connections[$db];
    $result = mysqli_autocommit($link, false);
    $result = $result && mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
    if (!$result) {
        error_log("Can't start a transaction: [" . mysqli_errno($link) . "] " . mysqli_error($link));
    }
    return $result;
}

function rollback_transaction($db) {
    global $database_connections;
    $result = mysqli_rollback($database_connections[$db]);
    if (!$result) {
        error_log("Can't start a transaction: [" . mysqli_errno($link) . "] " . mysqli_error($link));
    }
}

function end_transaction($db) {
    global $database_connections;
    mysqli_commit($database_connections[$db]);
}
