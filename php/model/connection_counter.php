<?php

function create_connection_counter($ip) {
    return query(MEMCACHED,
        "INSERT INTO connection_counters (remote_addr, last_access_timestamp) VALUES (?, UNIX_TIMESTAMP())", 's', $ip);
}

function get_connection_counter_by_ip($ip) {
    return single_result(query(MEMCACHED,
        "SELECT last_access_timestamp, requests_count FROM connection_counters WHERE remote_addr = ?", 's', $ip));
}

function increment_connection_counter($ip) {
    return query_multiple_params(MEMCACHED,
        "UPDATE connection_counters 
            SET last_access_timestamp = UNIX_TIMESTAMP(), 
                requests_count = requests_count + 1
            WHERE remote_addr = ?", 's', $ip);
}

function drop_connection_counter($ip) {
    return query_multiple_params(MEMCACHED,
        "UPDATE connection_counters 
            SET last_access_timestamp = UNIX_TIMESTAMP(), 
                requests_count = 1
            WHERE remote_addr = ?", 's', $ip);
}