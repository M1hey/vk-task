<?php

function json_respond_success($respond_assoc) {
    $respond_assoc['success'] = true;

    json_respond($respond_assoc);
}

function json_respond_fail($reason) {
    json_respond(['success' => false, 'msg' => $reason]);
}

function json_respond($respond_assoc) {
    header('Content-Type: application/json');
    echo json_encode($respond_assoc);
}