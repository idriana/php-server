<?php

declare(strict_types=1);
ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'POST') {
    http_response_code(400);
    error_log('edititem: неверный метод');
    die();
} else {
    if (array_key_exists('id', $_POST)) {
        $data = $_POST;
    } else {
        $json = NULL;
        while (!isset($json))
            $json = file_get_contents("php://input");
        $data = json_decode($json, true);
    }
}

if (!isset($data) or (!array_key_exists('id', $data) or $data["id"] == "")) {
    http_response_code(400);
    error_log('edititem2: недостаточно данных');
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/edititem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Найдите запись для ее редактирования"));
    header($header);
    die('{"status": "error", "message": "Failed to edit record"}');
}

try {
    $dbh = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(400);
    error_log('edititem2: '."Ошибка подключения к БД");
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/edititem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внутренняя ошибка", "id" => $data["id"]));
    header($header);
    die('[]');
}

try {
    $query_array = array();
    $form_query = array();
    $query_array[":chat_id"] = $data["id"];
    if (array_key_exists('chat_name', $data) and $data['chat_name'] != "") {
        $query_array[":chat_name"] = $data['chat_name'];
        $form_query['chat_name'] = ":chat_name";
    }
    if (array_key_exists('description', $data)  and $data['description'] != "") {
        $query_array[":description"] = $data['description'];
        $form_query['description'] = ":description";
    }
    if (array_key_exists('rating', $data) and $data['rating'] != "") {
        $query_array[":rating"] = $data['rating'];
        $form_query['rating'] = ":rating";
    }
    if (array_key_exists('country_id', $data) and $data['country_id'] != "") {
        $query_array[":country_id"] = $data['country_id'];
        $form_query['country_id'] = ":country_id";
    }
    if (array_key_exists('modes_id', $data) and $data['modes_id'] != "") {
        $query_array[":modes_id"] = $data['modes_id'];
        $form_query['modes_id'] = ":modes_id";
    }
    $values = http_build_query($form_query);
    $values = str_replace("%3A", ":", $values);
    $values = str_replace("&", ", ", $values);
    error_log('UPDATE chat SET '.$values.' WHERE chat.id = :chat_id');
    #$values = implode(', ', array_keys($query_array));
    #$into = str_replace(":", "", $values);
    $query = 'UPDATE chat SET '.$values.' WHERE chat.id = :chat_id';
    $sth = $dbh->prepare($query);
    $sth->execute($query_array);
    echo '{"status": "success","id": '.$data["id"].'}';
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/getitem.php?id=".$data["id"];
    header($header);
    die();
} catch (PDOException $e) {
    error_log('edititem2: '.$e);
    http_response_code(400);
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/edititem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внутренняя ошибка", "id" => $data["id"]));
    header($header);
    die('{"status": "error", "message": "Failed to edit record"}');
} catch (Exception $e){
    error_log('edititem2: '.$e);
    http_response_code(400);
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/edititem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внутренняя ошибка", "id" => $data["id"]));
    header($header);
    die('{"status": "error", "message": "Failed to edit record"}');
}