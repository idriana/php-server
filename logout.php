<?php

declare(strict_types=1);
ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'POST') {
    http_response_code(400);
    error_log('login: неверный метод');
    die();
}

try {
    session_name("auth");
    session_start();

    if (!isset($_SESSION["is_auth"])){
        error_log("logout: Пользователь не авторизован", 3, __DIR__."/security.log");
        die("OK");
    }

    session_destroy();
    echo "OK";

} catch (Exception $e){
    error_log('additem2: '.$e);
    http_response_code(400);
    die("[]");
}
