<?php

require_once '../safeplace/settings.inc.php';
require_once  MODULES_DIR.'autoloader.php';

//use MyApp\EventHandler;
use app\EventHandler; // uncomment to use modified even handler
//use MyApp\Logger\LoggerBuffer;
//use MyApp\Logger\FileLoggerBuf;

//$logger = new LoggerBuffer(1);
//$logger = new FileLoggerBuf('tmp.log', 1);

try {
//    $app = new EventHandler($dbSettings, $logger);
    $app = new EventHandler($dbSettings);//, $logger); // uncomment to use modified even handler
    $app->run();
}
catch (Exception $e) {
    //$logger->logEvent($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
    echo json_encode([]);
}