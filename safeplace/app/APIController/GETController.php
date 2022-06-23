<?php

namespace app\APIController;

use app\APIController\APIBasics;
use app\DBController\DBController;
use PDO;

class GETController extends APIBasics
{
    public function __construct(PDO $dbh){
        var_dump(isset($dbh));
        $this->dbc = new DBController($dbh);
    }

    public function list(){
        //$this->logger->logEvent('inside list', __FILE__, __LINE__, __FUNCTION__);
        $data = $this->ecr($_GET);
        if (!$this->CheckMethod('GET'))
            return [];
        $res = $this->dbc->Select($data);
        return $res;
    }

    public function get(){
        $data = $this->ecr($_GET);
        if (!$this->CheckMethod('GET') or !$this->CheckValues(['id'], $data))
            return [];
        if (array_key_exists("id", $data) and $data['id'] != "") {
            $res1 = $this->dbc->SelectChat($data);
            $res2 = $this->dbc->SelectUsers($data);
            return array($res1, $res2);
        } else
            return array(0, 0);
    }
}