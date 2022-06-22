<?php
declare(strict_types=1);
ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'GET') {
    http_response_code(400);
    error_log('login: неверный метод');
    die();
}

session_name("auth");
session_start();
if (! isset($_SESSION['is_auth'])) {
    error_log("Попытка получения доступа под анонимной сессией: ".session_id(), 3, __DIR__."/security.log");
    http_response_code(403);
    die("Forbidden");
}

echo "OK";