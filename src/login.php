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
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/index.php";
    header($header);
    die();
}

//echo "tpl1<br>";
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

$data = [
    'username' => 'Anonymous',
    'index_url' => 'http://'.$_SERVER['HTTP_HOST'],
    'auth_url' => 'http://'.$_SERVER['HTTP_HOST']."/src/login.php",
    'list_url' => 'http://'.$_SERVER['HTTP_HOST']."/src/listitems.php",
    'auth_name' => "Log In",
    'action' => 'http://'.$_SERVER['HTTP_HOST']."/src/api/login.php",
];

if (array_key_exists("error", $_GET))
    $data['error'] = true; //["error" => true, "error_text"=>$_GET["error_text"]];
if (array_key_exists("error_text", $_GET))
    $data['error_text'] = $_GET["error_text"]; //["error" => true];

$str = $twig->render('login.html', $data);
echo $str;
