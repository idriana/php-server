<?php

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'GET') {
    http_response_code(400);
    error_log('additem: неверный метод');
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

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

$data = [
    'values' => $_GET,
    'index_url' => 'http://'.$_SERVER['HTTP_HOST'],
    'list_url' => 'http://'.$_SERVER['HTTP_HOST']."/src/listitems.php",
    'action' => 'http://'.$_SERVER['HTTP_HOST'].'/src/api/additem2.php',
    'username' => $username,
    'auth_url' => $auth_url,
    'auth_name' => $auth_name,
];

$str = $twig->render('additem.html', $data);
echo $str;
