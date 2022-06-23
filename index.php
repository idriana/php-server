<?php

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'GET') {
    http_response_code(400);
    error_log('login: неверный метод');
    die();
}

require_once __DIR__.'/vendor/autoload.php';
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

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader);

$data = [
    'username' => $username,
    'auth_url' => $auth_url,
    'auth_name' => $auth_name,
    'auth_type' => $auth_type,
    'index_url' => 'http://'.$_SERVER['HTTP_HOST'],
    'list_url' => 'http://'.$_SERVER['HTTP_HOST']."/src/listitems.php",
    'element_url' => 'http://'.$_SERVER['HTTP_HOST']."/src/getitem.php?id=1",
    'add_element' => 'http://'.$_SERVER['HTTP_HOST']."/src/additem.php",
    'edit_element' => 'http://'.$_SERVER['HTTP_HOST']."/src/edititem.php",
    'delete_element' => 'http://'.$_SERVER['HTTP_HOST']."/src/deleteitem.php"
];

$str = $twig->render('index.html', $data);
echo $str;