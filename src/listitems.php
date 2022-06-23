<?php

ini_set('error_log', __DIR__ . '/error.log');

$method = $_SERVER['REQUEST_METHOD'];
if ($method != 'GET') {
    http_response_code(400);
    error_log('login: неверный метод');
    die();
}

$data = $_GET;

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
}
catch (Exception $e) {
    http_response_code(400);
    error_log('listitems: '."Ошибка подключения к БД");
    die('[]');
}

try {
    $query_array = array();
    $form_query = array();
    if (array_key_exists('id', $data) and $data['id'] != "") {
        $query_array[":id"] = $data['id'];
        $form_query['chat.id'] = ":id";
    }
    if (array_key_exists('chat_name', $data) and $data['chat_name'] != "") {
        $query_array[":chat_name"] = "%".$data['chat_name']."%";
        $form_query['chat.chat_name'] = ":chat_name";
    }
    if (array_key_exists('description', $data) and $data['description'] != "") {
        $query_array[":description"] = "%".$data['description']."%";
        $form_query['description'] = ":description";
    }
    if (array_key_exists('rating', $data) and $data['rating'] != "") {
        $query_array[":rating"] = $data['rating'];
        $form_query['rating'] = ":rating";
    }
    if (array_key_exists('country', $data) and $data['country'] != "") {
        $query_array[":country_name"] = "%".$data['country']."%";
        $form_query['country_name'] = ":country_name";
    }
    if (array_key_exists('modes', $data) and $data['modes'] != "") {
        $query_array[":modes"] = "%".$data['modes']."%";
        $form_query['mode_name'] = ":modes";
    }
    $where_sth = http_build_query($form_query);
    $where_sth = str_replace("%3A", ":", $where_sth);
    $where_sth = str_replace("&", " and ", $where_sth);
    $where_sth = str_replace("rating=", "rating>=", $where_sth);
    $where_sth = str_replace("chat_name=", "chat_name like ", $where_sth);
    $where_sth = str_replace("description=", "description like ", $where_sth);
    $where_sth = str_replace("country_name=", "country_name like ", $where_sth);
    $where_sth = str_replace("mode_name=", "mode_name like ", $where_sth);
    $query = 'SELECT chat.id, 
        chat.chat_name, 
        chat.description, 
        chat.rating, 
        country.country_name as ref1name, 
        modes.mode_name as ref2name 
        FROM chat
        LEFT JOIN country ON country.id = chat.country_id
        LEFT JOIN modes ON modes.id = chat.modes_id';
    if ($where_sth != "")
        $sth = $dbh->prepare($query.' WHERE '.$where_sth);
    else
        $sth = $dbh->prepare($query);
    $sth->execute($query_array);
    $res = $sth->fetchAll();
    $res = json_decode(json_encode($res), true);
} catch (PDOException $e) {
    error_log('listitem: '.$e);
    http_response_code(400);
    die('[]');
} catch (Exception $e){
    error_log('listitem: '.$e);
    http_response_code(400);
    die('[]');
}

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

$data = [
    'values' => $_GET,
    'index_url' => 'http://'.$_SERVER['HTTP_HOST'],
    'get_url' => 'http://'.$_SERVER['HTTP_HOST'].'/src/getitem.php',
    'edit_url' => 'http://'.$_SERVER['HTTP_HOST'].'/src/edititem.php',
    'delete_url' => 'http://'.$_SERVER['HTTP_HOST'].'/src/deleteitem.php',
    'list_url' => 'http://'.$_SERVER['HTTP_HOST']."/src/listitems.php",
    'username' => $username,
    'auth_url' => $auth_url,
    'auth_name' => $auth_name
];

if (isset($res) and $res != []) {
    $offset = 0;
    $count = 30;
    if (array_key_exists("items_per_page", $_GET) and intval($_GET["items_per_page"]) > 0)
        $count = intval($_GET["items_per_page"]);
    if (array_key_exists("page_number", $_GET) and intval($_GET["page_number"])-1 > 0)
        $offset = min(intdiv(count($res), $count), intval($_GET["page_number"])-1);
    $data['chats'] = array_slice($res, $offset*$count, $count);
    $data['values']['items_per_page'] = $count;
    $data['values']['page_number'] = $offset+1;
} else {
    $data['values']['items_per_page'] = 30;
    $data['values']['page_number'] = 1;
    $data['error'] = true;
}

$str = $twig->render('listitems.html', $data);
echo $str;