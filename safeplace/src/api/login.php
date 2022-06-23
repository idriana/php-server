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
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/login.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Заполните поля обозначенные *"));
    header($header);
    die();
}

try {
    $dbh = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(400);
    error_log('login: '."Ошибка подключения к БД");
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/login.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внутренняя ошибка"));
    header($header);
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
        $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/login.php";
        $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Неверный логин"));
        header($header);
        die("Invalid credentials");
    }
    if (!password_verify($data["password"], $res[0]->password)){
        http_response_code(200);
        error_log('login: '."Неверный пароль", 3, __DIR__."/security.log");
        $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/login.php";
        $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Неверный пароль"));
        header($header);
        die("Invalid credentials");
    }

    session_name("auth");
    session_set_cookie_params([
        'lifetime' => 600,
        'httponly' => true,
        'sameSite' => 'strict'
    ]);
    session_start();
    session_regenerate_id();
    $_SESSION['username'] = $data["username"];
    error_log(json_encode($_POST), 3, "post.log");
    echo "OK";
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/login.php";
    header($header);
    die();

} catch (PDOException $e) {
    error_log('login: '."Ошибка запроса к базе");
    http_response_code(400);
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/login.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внутренняя ошибка"));
    header($header);
    die("[]");
} catch (Exception $e){
    error_log('additem2: '.$e);
    http_response_code(400);
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/login.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внутренняя ошибка"));
    header($header);
    die("[]");
}