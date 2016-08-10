<?php

require_once 'db.php';
require_once 'user/user.php';

function create_auth_token($user_id, $new_token, $validator_hash) {
    return query_multiple_params(AUTH_TOKEN_DB,
        "INSERT INTO auth_tokens (user_id, token, validator_hash)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
            token = VALUES(token),
            validator_hash = VALUES(validator_hash)",
        'iss', $user_id, $new_token, $validator_hash);
}

function update_auth_token($token_id, $new_token) {
    return query_multiple_params(AUTH_TOKEN_DB,
        "UPDATE auth_tokens AS tokens
            SET tokens.token = ?
            WHERE tokens.id = ?",
        'si', $new_token, $token_id);
}

function get_auth_token($token) {
    $query = query(AUTH_TOKEN_DB,
        "SELECT user_id, id, validator_hash FROM auth_tokens AS tokens
          WHERE tokens.token = ?",
        's', $token);
    if ($query) {
        return $query[0];
    } else {
        return false;
    }
}

function generate_token($length = 32) {
    return bin2hex(openssl_random_pseudo_bytes($length >> 1)); // bin2hex returns x2 of input length
}