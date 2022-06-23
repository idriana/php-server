<?php

declare(strict_types=1);
ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'POST') {
    http_response_code(400);
    error_log('deleteitem2: неверный метод');
    die();
} else {
    $data = $_POST;
}

if (!isset($data) or (!array_key_exists('id', $data) or $data['id'] == "")) {
    http_response_code(400);
    error_log('deleteitem2: недостаточно данных');
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/deleteitem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Заполните поля, отмеченные *"));
    header($header);
    die('{"status": "error", "message": "Failed to delete record"}');
}

try {
    $dbh = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(400);
    error_log('deleteitem2: '."Ошибка подключения к БД");
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/deleteitem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внтренняя ошибка", "id"=>$data['id']));
    header($header);
    die('[]');
}

try {
    $query_array = array();
    $query_array[":chat_id"] = $data["id"];
    $query = 'SELECT * FROM user_chat WHERE user_chat.chat_id = :chat_id';
    $sth = $dbh->prepare($query);
    $sth->execute($query_array);
    $res = $sth->fetchAll();
    if ($res == []) {

        echo '{"status": "success"}';
        $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/getitem.php?id=".$data["id"];
        header($header);
        die();
    } else {
        $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/deleteitem.php";
        $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Невозможно удалить связанную запись", "id"=>$data['id']));
        header($header);
        die('{"status": "error", "message": "Failed to delete record"}');
    }
} catch (PDOException $e) {
    error_log('deleteitem2: '."Ошибка запроса к базе");
    http_response_code(400);
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/deleteitem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внтренняя ошибка", "id"=>$data['id']));
    header($header);
    die('{"status": "error", "message": "Failed to delete record"}');
} catch (Exception $e){
    error_log('deleteitem2: '.$e);
    http_response_code(400);
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/edititem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внтренняя ошибка", "id"=>$data['id']));
    header($header);
    die('{"status": "error", "message": "Failed to delete record"}');
}