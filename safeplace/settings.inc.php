<?php

define('DEBUG', false);
define('MODULES_DIR', '../safeplace/');
require_once __DIR__.'/vendor/autoload.php';

$dbSettings = [
    'connectionString' => 'mysql:host=localhost;dbname=mydb;charset=utf8',
    'dbUser' => 'root',
    'dbPwd' => 'root'
];
