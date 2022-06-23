<?php
declare(strict_types=1);
ini_set('error_log', __DIR__ . '/error.log');

if ($_SERVER['REQUEST_METHOD'] != 'GET'){
    http_response_code(400);
    error_log('listitems2: неверный метод');
    die();
}

try {
    $dbh = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
}
catch (Exception $e) {
    http_response_code(400);
    error_log('listitems2: '."Ошибка подключения к БД");
    die('[]');
}

try {
    $query =
        'SELECT 
        chat.id as id,
        chat.chat_name, chat.description, chat.rating, chat.create_time,
        country.id as ref1id, 
        country.country_name as ref1name,
        modes.id as ref2id,
        modes.name as ref2name
        FROM chat 
        LEFT JOIN country 
        on chat.country_id = country.id
        LEFT JOIN modes
        on chat.modes_id = modes.id';
    $sth = $dbh->prepare($query);
    $sth->execute();
    $res = $sth->fetchAll();
    echo json_encode($res);
} catch (PDOException $e) {
    error_log('listitem2: '.$e); #"Ошибка запроса к базе"
    http_response_code(400);
    die('[]');
} catch (Exception $e){
    error_log('listitem2: '.$e);
    http_response_code(400);
    die('[]');
}
