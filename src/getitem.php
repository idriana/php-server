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
    $query = 'SELECT *, 
            chat.id as id,
            country.id as ref1id,
            country.country_name as ref1name,
            modes.id as ref2id,
            modes.mode_name as ref2name
            FROM chat
            LEFT JOIN country ON chat.country_id = country.id
            LEFT JOIN modes ON chat.modes_id = modes.id
            where chat.id = :chat_id';
    $sth = $dbh->prepare($query);
    $sth->execute(array('chat_id' => $_GET['id']));
    $res = $sth->fetchAll();
    $query = 'SELECT user.id as id, 
            user.username,
            user.email,
            user.age,
            chat.chat_name
            FROM user
            LEFT JOIN user_chat ON user.id = user_chat.user_id
            LEFT JOIN chat ON chat.id = user_chat.chat_id
            where chat.id = :chat_id';
    $sth = $dbh->prepare($query);
    $sth->execute(array('chat_id' => $_GET['id']));
    $res2 = $sth->fetchAll();
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

$data = [
    'values' => $_GET,
    'index_url' => 'http://'.$_SERVER['HTTP_HOST'],
    'edit_url' => 'http://'.$_SERVER['HTTP_HOST'].'/src/edititem.php',
    'delete_url' => 'http://'.$_SERVER['HTTP_HOST'].'/src/deleteitem.php',
    'list_url' => 'http://'.$_SERVER['HTTP_HOST']."/src/listitems.php",
    'username' => $username,
    'auth_url' => $auth_url,
    'auth_name' => $auth_name,
    'auth_type' => $auth_type,
];

if (isset($res) and $res != []) {
    $data['chats'] = $res;
    if (isset($res2) and $res2 != [])
        $data['users'] = $res2;
    else
        $data['users_error'] = true;
} else {
    $data['error'] = true;
}

$str = $twig->render('getitem.html', $data);
echo $str;