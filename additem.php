<?php
declare(strict_types=1);
ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'PUT' and $method != 'POST') {
    http_response_code(400);
    error_log('additem: неверный метод');
    die();
} else {
    if ($method == 'POST') {
        $data = $_POST;
    }
    else {
        $json = NULL;
        while (!isset($json))
            $json = file_get_contents("php://input");
        $data = json_decode($json, true);
    }
}

if (!isset($data) or (!array_key_exists('username', $data) or !array_key_exists('password', $data))){
    http_response_code(400);
    error_log('additem: недостаточно данных');
    die('{"status": "error", "message": "Failed to add record"}');
}



try {
    $dbh = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    error_log('additem: '."Ошибка подключения к БД");
    http_response_code(400);
    die('[]');
}

try {
    $query_array = array();
    $query_array[":username"] = $data['username'];
    $query_array[":password"] = password_hash($data['password'], PASSWORD_DEFAULT);
    $values = implode(', ', array_keys($query_array));
    $into = str_replace(":", "",  $values);
    $query = 'INSERT INTO user ('.$into.') VALUES ('.$values.')';
    $sth = $dbh->prepare($query);
    $sth->execute($query_array);
    echo '{"status": "success","id": '.$dbh->lastInsertId().'}';
} catch (PDOException $e) {
    error_log('additem: '.$e);
    http_response_code(400);
    die('{"status": "error", "message": "Failed to add record"}');
} catch (Exception $e){
    error_log('additem: '.$e);
    http_response_code(400);
    die('{"status": "error", "message": "Failed to add record"}');
}