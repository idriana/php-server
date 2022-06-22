<?php

declare(strict_types=1);
ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'POST') {
    http_response_code(400);
    error_log('additem2: неверный метод');
    die();
} else {
    if ($method == 'POST') {
        $data = $_POST;
    } else {
        $json = NULL;
        while (!isset($json))
            $json = file_get_contents("php://input");
        $data = json_decode($json, true);
    }
}

if (!isset($data) or (!array_key_exists('chat_name', $data))) {
    http_response_code(400);
    error_log('additem2: недостаточно данных');
    die('{"status": "error", "message": "Failed to add record"}');
}

try {
    $dbh = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(400);
    error_log('additem2: '."Ошибка подключения к БД");
    die('[]');
}

try {
    $query_array = array();
    $query_array[":chat_name"] = $data['chat_name'];
    if (array_key_exists('description', $data))
        $query_array[":description"] = $data['description'];
    if (array_key_exists('rating', $data))
        $query_array[":rating"] = $data['rating'];
    if (array_key_exists('country_id', $data))
        $query_array[":country_id"] = $data['country_id'];
    if (array_key_exists('modes_id', $data))
        $query_array[":modes_id"] = $data['modes_id'];
    $values = implode(', ', array_keys($query_array));
    $into = str_replace(":", "", $values);
    $query = 'INSERT INTO chat (' . $into . ') VALUES (' . $values . ')';
    $sth = $dbh->prepare($query);
    $sth->execute($query_array);
    echo '{"status": "success","id": '.$dbh->lastInsertId().'}';
} catch (PDOException $e) {
    error_log('additem2: '."Ошибка запроса к базе");
    http_response_code(400);
    die('{"status": "error", "message": "Failed to add record"}');
} catch (Exception $e){
    error_log('additem2: '.$e);
    http_response_code(400);
    die('{"status": "error", "message": "Failed to add record"}');
}