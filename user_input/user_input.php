<?php
    echo 'Запрошена страница '.basename($_SERVER['PHP_SELF']).'<br><br>';

    if ($_SERVER['REQUEST_METHOD'] != 'POST'){
        die('Запрещено, неверный метод');
    }
    if (!array_key_exists('HTTP_X_ACCESS_TOKEN', $_SERVER)){
        die('Запрещено, не задан токен');
    }
    if ($_SERVER['HTTP_X_ACCESS_TOKEN'] != 'SECRET_TOKEN'){
        die('Запрещено, неверный токен');
    }
    if (!array_key_exists('page', $_GET)){
        die('Ошибка, не задан тип страницы');
    }
    if ($_GET['page'] != 'page1' and $_GET['page'] != 'page2' and $_GET['page'] != 'page3'){
        die('Ошибка, недопустимая страница');
    }
    if (!array_key_exists('HTTP_CONTENT_TYPE', $_SERVER) or
        ($_SERVER['HTTP_CONTENT_TYPE'] != 'application/x-www-form-urlencoded')){
        die('Ошибка, неверный тип данных');
    }
    if (count($_POST) == 0){
        die('Ошибка, данные не заданы');
    } else {
        echo 'Через POST передано '.count($_POST).' переменных <br><br>';
        foreach ($_POST as $key => $value){
            echo htmlentities('значение['.$key.']: '.$value).'<br><br>';
        }
    }
