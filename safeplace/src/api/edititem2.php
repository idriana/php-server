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