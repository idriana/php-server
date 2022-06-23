<?php

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
    $auth_url = 'http://'.$_SERVER['HTTP_HOST']."/src/api/logout.php";
    $auth_name = "Log Out";
} else {
    $username = 'Anonymous';
    $auth_url = 'http://'.$_SERVER['HTTP_HOST']."/src/login.php";
    $auth_name = "Log In";
}

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

$data = [
    'values' => $_GET,
    'index_url' => 'http://'.$_SERVER['HTTP_HOST'],
    'username' => $username,
    'auth_url' => $auth_url,
    'auth_name' => $auth_name,
    'action' => 'http://'.$_SERVER['HTTP_HOST'].'/src/api/deleteitem2.php',
    'list_url' => 'http://'.$_SERVER['HTTP_HOST']."/src/listitems.php",

];

if (array_key_exists("id", $_GET) and $_GET["id"] != "")
    $data['get_url'] = 'http://'.$_SERVER['HTTP_HOST']."/src/getitem.php?id=".$_GET["id"];

$str = $twig->render('deleteitem.html', $data);
echo $str;