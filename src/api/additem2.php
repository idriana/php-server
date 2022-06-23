<?php

declare(strict_types=1);
ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'PUT' and $method != 'POST') {
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

if (!isset($data) or (!array_key_exists('chat_name', $data)) or $data['chat_name'] == "") {
    http_response_code(400);
    error_log('additem2: недостаточно данных');
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/additem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Заполните поля обозначенные *"));
    $header = $header."&".http_build_query($_POST);
    header($header);
    die('{"status": "error", "message": "Failed to add record"}');
}

try {
    $dbh = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(400);
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/additem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внутренняя ошибка"));
    $header = $header."&".http_build_query($_POST);
    header($header);
    die('[]');
}

try {
    $query_array = array();
    $query_array[":chat_name"] = $data['chat_name'];
    if (array_key_exists('description', $data) and $data['description'] != "")
        $query_array[":description"] = $data['description'];
    if (array_key_exists('rating', $data) and $data['rating'] != "" and floatval($data['rating'] != 0))
        $query_array[":rating"] = floatval($data['rating']);
    if (array_key_exists('country_id', $data) and $data['country_id'] != "")
        $query_array[":country_id"] = intval($data['country_id']);
    if (array_key_exists('modes_id', $data) and $data['modes_id'] != "")
        $query_array[":modes_id"] = intval($data['modes_id']);
    error_log(json_encode($query_array), 3, "post.log");
    $values = implode(', ', array_keys($query_array));
    $into = str_replace(":", "", $values);
    $query = 'INSERT INTO chat (' . $into . ') VALUES (' . $values . ')';
    $sth = $dbh->prepare($query);
    $sth->execute($query_array);
    echo '{"status": "success","id": '.$dbh->lastInsertId().'}';
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/getitem.php";
    $header = $header."?".http_build_query(array("id"=>$dbh->lastInsertId()));
    header($header);
    die();
} catch (PDOException $e) {
    error_log('additem2: '."Ошибка запроса к базе");
    http_response_code(400);
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/additem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внутренняя ошибка"));
    $header = $header."&".http_build_query($_POST);
    header($header);
    die('{"status": "error", "message": "Failed to add record"}');
} catch (Exception $e){
    error_log('additem2: '.$e);
    http_response_code(400);
    $header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/additem.php";
    $header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Внутренняя ошибка"));
    $header = $header."&".http_build_query($_POST);
    header($header);
    die('{"status": "error", "message": "Failed to add record"}');
}