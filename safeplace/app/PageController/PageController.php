<?php

namespace app\PageController;

use PDO;
use app\APIController\GETController;
use app\APIController\POSTController;
use app\DBController\DBController;

class PageController extends GETController
{
    public function __construct(PDO $dbh){
        $this->loader = new \Twig\Loader\FilesystemLoader('../safeplace/templates');
        $this->twig = new \Twig\Environment($this->loader);
        $this->dbh = $dbh;
        $this->dbc = new DBController($dbh);
        $this->pc = new POSTController($dbh);
    }

    private function get_session(){
        session_name("auth");
        session_start();
        if (isset($_SESSION['username'])){
            return array($_SESSION['username'], "http://".$_SERVER['HTTP_HOST'].'/DOCUMENT_ROOT/?page=logout_api', "Log Out");
        }
        return array("Anonymous", "http://".$_SERVER['HTTP_HOST'].'/DOCUMENT_ROOT/?page=login', "Log In");
    }

    private function default_data(){
        $session = $this->get_session();
        $data = [
            'values' => $this->ecr($_GET),
            'get_url' => 'http://'.$_SERVER['HTTP_HOST'].'/DOCUMENT_ROOT/?page=getitem',
            'edit_url' => 'http://'.$_SERVER['HTTP_HOST'].'/DOCUMENT_ROOT/?page=edititem',
            'add_url' => 'http://'.$_SERVER['HTTP_HOST'].'/DOCUMENT_ROOT/?page=additem',
            'delete_url' => 'http://'.$_SERVER['HTTP_HOST'].'/DOCUMENT_ROOT/?page=deleteitem',
            'list_url' => 'http://'.$_SERVER['HTTP_HOST']."/DOCUMENT_ROOT/?page=listitems",
            'index_url' => 'http://'.$_SERVER['HTTP_HOST']."/DOCUMENT_ROOT/?page=index",
            'username' => $session[0],
            'auth_url' => $session[1],
            'auth_name' => $session[2]
        ];
        $data = array_merge($data, $this->ecr($_GET));
        return $data;
    }

    public function index(){
        $data = $this->default_data();

        $str = $this->twig->render('index.html', $data);
        echo $str;
    }

    public function listitems(){
        $data = $this->default_data();

        $res = $this->list();
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

        $str = $this->twig->render('listitems.html', $data);
        echo $str;
    }

    public function getitem(){
        $data = $this->default_data();

        $res = $this->get();
        if (isset($res) and isset($res[0]) and!empty($res[0])) {
            $data['chats'] = $res[0];
            if (isset($res[1]) and !empty($res[1]))
                $data['users'] = $res[1];
            else
                $data['users_error'] = true;
        } else {
            $data['error'] = true;
        }

        $str = $this->twig->render('getitem.html', $data);
        echo $str;
    }

    public function additem(){
        $data = $this->default_data();
        $data['action'] = 'http://'.$_SERVER['HTTP_HOST'].'/DOCUMENT_ROOT/?page=additem_api';

        $str = $this->twig->render('additem.html', $data);
        echo $str;
    }

    public function edititem(){
        $data = $this->default_data();
        $data['action'] = 'http://'.$_SERVER['HTTP_HOST'].'/DOCUMENT_ROOT/?page=edititem_api';

        if (array_key_exists("id", $data) and $data["id"]!="")
            $res = $this->get()[0];
        else
            $res = [];
        if (isset($res) and !empty($res)) {
            $data['values'] = array_merge($data['values'], $res[0]);
        } else {
            $data['error'] = true;
        }

        $str = $this->twig->render('edititem.html', $data);
        echo $str;
    }

    public function deleteitem(){
        $data = $this->default_data();
        $data['action'] = 'http://'.$_SERVER['HTTP_HOST'].'/DOCUMENT_ROOT/?page=deleteitem_api';

        if (array_key_exists("id", $_GET) and $_GET["id"] != "")
            $data['get_url'] = 'http://'.$_SERVER['HTTP_HOST']."/DOCUMENT_ROOT/?page=getitem&id=".$_GET["id"];

        $str = $this->twig->render('deleteitem.html', $data);
        echo $str;
    }

    public function login(){
        $data = $this->default_data();
        $data["username"] = "Anonymous";
        $data['action'] = 'http://'.$_SERVER['HTTP_HOST']."/DOCUMENT_ROOT/?page=login_api";

        if (array_key_exists("error", $_GET))
            $data['error'] = true; //["error" => true, "error_text"=>$_GET["error_text"]];
        if (array_key_exists("error_text", $_GET))
            $data['error_text'] = $_GET["error_text"]; //["error" => true];

        $str = $this->twig->render('login.html', $data);
        echo $str;
    }

    public function login_api(){
        $this->pc->login();
    }

    public function logout_api(){
        $this->pc->logout();
    }

    public function additem_api(){
        $this->pc->add();
    }

    public function edititem_api(){
        $this->pc->edit();
    }

    public function deleteitem_api(){
        $this->pc->delete();
    }
}