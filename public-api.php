<?php

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'GET') {
    http_response_code(400);
    error_log('login: неверный метод');
    die();
}

echo "OK";
