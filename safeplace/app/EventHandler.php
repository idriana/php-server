<?php

namespace app;

//use app\Logger\LoggerInterface;
use PDO;
use Exception;

class EventHandler
{
    private string $page;
    private PDO $dbh;
    //protected LoggerInterface $logger;
    protected array $handler;

    public function __construct(array $dbSettings)//, LoggerInterface $logger)
    {
        //$this->logger = $logger;
        $page = array_key_exists('page', $_GET) ? $_GET['page'] : '';
        $pages = array('index', 'listitems', 'getitem', 'edititem', 'additem', 'deleteitem',
            'edititem_api', 'additem_api', 'deleteitem_api', 'login', 'login_api', 'logout_api');
        // инициализация базы
        $this->initDB( $dbSettings['connectionString'], $dbSettings['dbUser'], $dbSettings['dbPwd'] );
        if (in_array($page, $pages))
            $this->setPage($page);
        elseif ($page == ""){
            $page = "index";
            $this->setPage($page);
        } else{
            http_response_code(400);
            die();
        }
    }

    private function setPage( string $page ) {
        if( !empty($page) ) {
            $this->page = $page;
            $this->handler = [
                'page' => $page,
                'controller' => 'PageController',
                'handler' => $page
            ];
        }

        //$this->logger->logEvent('handler: '.var_export($this->handler, true), __FILE__, __LINE__, __FUNCTION__);
    }

    private function createController()
    {
        // указываем полный name
        $controller = 'app\PageController\PageController';
        //$this->logger->logEvent('going to create '.$controller, __FILE__, __LINE__, __FUNCTION__);
        $params = [
            $this->dbh,
            //$this->logger
        ];
        //return new $controller(...$params);
        return new $controller($this->dbh);//, $this->logger);
    }

    private function getHandlerFunction()
    {
        return $this->handler['handler'];
    }

    private function initDB( string $connectionString, string $dbUser, string $dbPwd )
    {
        // создание подключения через connection_string с указанием типа базы
        $this->dbh = new PDO( $connectionString, $dbUser, $dbPwd );
        $this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //$this->logger->logEvent('Connected to DB!', __FILE__, __LINE__, __FUNCTION__);
    }

    /**
     * call handler to process request
     */
    public function run()
    {
        try {
            $controller = $this->createController();
            $handler = $this->getHandlerFunction();
            echo $controller->$handler();
        }
        catch (Exception $e) {
            //$this->logger->logEvent($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
            echo $e;
            echo json_encode([]);
        }
    }
}