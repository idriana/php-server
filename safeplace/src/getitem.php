<?php

ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'GET') {
    http_response_code(400);
    error_log('login: неверный метод');
    die();
}

require_once '../vendor/autoload.php';
session_name("auth");
session_start();

if (isset($_SESSION["username"])){
    $username = $_SESSION["username"];
    $auth_type = false;
    $auth_url = 'http://'.$_SERVER['HTTP_HOST']."/src/api/logout.php";
    $auth_name = "Log Out";
} else {
    $username = 'Anonymous';
    $auth_type = true;
    $auth_url = 'http://'.$_SERVER['HTTP_HOST']."/src/login.php";
    $auth_name = "Log In";
}

try {
    $dbh = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (Exception $e) {
    error_log('getitem: '."Ошибка подключения к БД");
    http_response_code(400);
    die('[]');
}

try {
    if (!array_key_exists('id', $_GET)){
        http_response_code(400);
        throw new Exception("getitem: недостаточно данных");
    }

} catch (PDOException $e) {
    error_log('getitem: '."Ошибка запроса к базе");
    http_response_code(400);
    die('[]');
} catch (Exception $e){
    error_log('getitem: '.$e);
    http_response_code(400);
    die('[]');
}

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);
