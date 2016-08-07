<?php
//session_start();
require_once 'model/db.php';

global $database_connections;


$login = $_GET['login'];
$pwd = $_GET['pwd'];
$q = "SELECT id, login, username, password_hash, account_type, balance FROM users WHERE login=?";

$res = query(USERS_DB, $q, 's', $login);
if ($res) {
    echo var_dump($res) . '<br/>';
    if (password_verify($pwd, $res[3])) {
        echo 'Verified';
    } else {
        echo 'Not verified';
    }
} else {
    echo 'false';
}