<?php

ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'GET') {
    http_response_code(400);
    error_log('login: неверный метод');
    die();
}

$data = $_GET;


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
}
catch (Exception $e) {
    http_response_code(400);
    error_log('listitems: '."Ошибка подключения к БД");
    die('[]');
}

try {

    $res = json_decode(json_encode($res), true);
} catch (PDOException $e) {
    error_log('listitem: '.$e);
    http_response_code(400);
    die('[]');
} catch (Exception $e){
    error_log('listitem: '.$e);
    http_response_code(400);
    die('[]');
}



