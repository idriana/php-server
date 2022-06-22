<?php

declare(strict_types=1);
ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'POST') {
    http_response_code(400);
    error_log('login: неверный метод');
    die();
} else {
    $data = $_POST;
}

if (!isset($data) or (!array_key_exists('username', $data) or !array_key_exists('password', $data))) {
    http_response_code(400);
    error_log('login: недостаточно данных');
    die();
}

try {
    $dbh = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(400);
    error_log('login: '."Ошибка подключения к БД");
    die("[]");
}

try {
    $query_array = array();
    $query = 'SELECT password FROM user WHERE user.username = :username';
    $sth = $dbh->prepare($query);
    $sth->execute(array(":username"=>$data["username"]));
    $res = $sth->fetchAll();
    if (count($res) != 1){
        http_response_code(200);
        error_log('login: '."Неверный логин", 3, __DIR__."/security.log");
        die("Invalid credentials");
    }
    if (!password_verify($data["password"], $res[0]->password)){
        http_response_code(200);
        error_log('login: '."Неверный пароль", 3, __DIR__."/security.log");
        die("Invalid credentials");
    }

    session_name("auth");
    session_set_cookie_params([
        'lifetime' => 10,
        'httponly' => true,
        'sameSite' => 'strict'
    ]);
    session_start();
    session_regenerate_id();
    $_SESSION['is_auth'] = true;
    echo "OK";

} catch (PDOException $e) {
    error_log('login: '."Ошибка запроса к базе");
    http_response_code(400);
    die("[]");
} catch (Exception $e){
    error_log('additem2: '.$e);
    http_response_code(400);
    die("[]");
}