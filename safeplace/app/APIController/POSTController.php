<?php

namespace app\APIController;

use app\APIController\APIBasics;
use app\DBController\DBController;
use PDO;

class POSTController extends APIBasics
{
    public function __construct(PDO $dbh){
        $this->dbh=$dbh;
        $this->dbc = new DBController($dbh);
    }

    public function add(){
        $data = $this->ecr($_POST);
        if (!$this->CheckMethod('POST') or !$this->CheckValues(['chat_name'], $data))
            return [];
        if (array_key_exists('chat_name', $data) and $data['chat_name']!="") {
            $res = $this->dbc->Insert($data);
            $data["page"] = "getitem";
            $data["id"] = $this->dbh->lastInsertId();
        }
        else {
            $res = [];
            $data["page"] = "additem";
            $data['error'] = 1;
            $data['error_text'] = "недостаточно данных";
        }
        $this->redirect('/DOCUMENT_ROOT/', $data);
    }

    public function edit(){
        $data = $this->ecr($_POST);
        if (!$this->CheckMethod('POST') or !$this->CheckValues(['id'], $data))
            return [];
        $res = $this->dbc->Edit($data);
        if ($res == 1) {
            $data["page"] = "edititem";
            $data["error"] = true;
            $data["error_text"] = "ошибка бд";
        } else
            $data["page"] = "getitem";
        $this->redirect('/DOCUMENT_ROOT/', $data);
    }

    public function delete(){
        $data = $this->ecr($_POST);
        if (!$this->CheckMethod('POST') or !$this->CheckValues(['id'], $data))
            return [];
        $res = $this->dbc->Delete($data);
        if ($res == 1) {
            $data["error"] = 1;
            $data["error_text"] = "не прошел запрос к бд";
            $data["page"] = "deleteitem";
        } else {
            $data["page"] = "getitem";
        }
        $this->redirect('/DOCUMENT_ROOT/', $data);
        die();
    }

    public function login(){
        $data = $this->ecr($_POST);
        if (!$this->CheckMethod('POST') or !$this->CheckValues(['username, password'], $data)) {
            return [];
        }
        $res = $this->dbc->login($data);
        if (count($res) != 1){
            $data["page"] = "login";
            $data['error'] = true;
            $data['error_text'] = "неправильный логин";
            $this->redirect('/DOCUMENT_ROOT/', $data);
            die();
        }
        elseif (!password_verify($data["password"], $res[0]["password"])){
            $data["page"] = "login";
            $data['error'] = true;
            $data['error_text'] = "неправильный пароль";
            $this->redirect('/DOCUMENT_ROOT/', $data);
            die();
        } else {
            session_name("auth");
            session_set_cookie_params([
                'lifetime' => 600,
                'httponly' => true,
                'sameSite' => 'strict'
            ]);
            session_start();
            session_regenerate_id();
            $_SESSION['username'] = $data["username"];
            $data["page"] = "index";
            $this->redirect('/DOCUMENT_ROOT/', $data);
            die();
        }
    }

    public function logout(){
        session_name("auth");
        session_start();

        if (!isset($_SESSION["username"])){
            error_log("logout: Пользователь не авторизован", 3, __DIR__."/security.log");
            die("OK");
        }

        session_destroy();
        $data["page"] = "index";
        $this->redirect('/DOCUMENT_ROOT/', $data);
    }
}