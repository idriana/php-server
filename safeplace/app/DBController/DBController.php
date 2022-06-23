<?php

namespace app\DBController;

use PDO;

class DBController
{
    protected PDO $dbh;

    public function __construct( PDO $dbh)
    {
        // need to implement constructor
        $this->dbh = $dbh;
    }

    public function SelectUsers($data){
        $query_array = array('chat_id' => $data['id']);
        $query = 'SELECT user.id as id, 
            user.username,
            user.email,
            user.age,
            chat.chat_name
            FROM user
            LEFT JOIN user_chat ON user.id = user_chat.user_id
            LEFT JOIN chat ON chat.id = user_chat.chat_id
            where chat.id = :chat_id';
        return $this->queryFetchAll($query, $query_array);
    }

    public function SelectChat($data){
        $query_array = array('chat_id' => $data['id']);
        $query = 'SELECT *, 
            chat.id as id,
            country.id as ref1id,
            country.country_name as ref1name,
            modes.id as ref2id,
            modes.mode_name as ref2name
            FROM chat
            LEFT JOIN country ON chat.country_id = country.id
            LEFT JOIN modes ON chat.modes_id = modes.id
            where chat.id = :chat_id';
        return $this->queryFetchAll($query, $query_array);
    }

    public function Select($data){
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
            $query = $query.' WHERE '.$where_sth;
        $res = $this->queryFetchAll($query, $query_array);
        return $res;
    }

    public function Delete($data){
        $query_array = array();
        $query_array[":chat_id"] = $data["id"];
        $query = 'DELETE FROM chat WHERE chat.id = :chat_id';
        return $this->queryFetchAll($query, $query_array);
    }

    public function Edit($data){
        $query_array = array();
        $form_query = array();
        $query_array[":chat_id"] = $data["id"];
        if (array_key_exists('chat_name', $data) and $data['chat_name'] != "") {
            $query_array[":chat_name"] = $data['chat_name'];
            $form_query['chat_name'] = ":chat_name";
        }
        if (array_key_exists('description', $data)  and $data['description'] != "") {
            $query_array[":description"] = $data['description'];
            $form_query['description'] = ":description";
        }
        if (array_key_exists('rating', $data) and $data['rating'] != "") {
            $query_array[":rating"] = $data['rating'];
            $form_query['rating'] = ":rating";
        }
        if (array_key_exists('country_id', $data) and $data['country_id'] != "") {
            $query_array[":country_id"] = $data['country_id'];
            $form_query['country_id'] = ":country_id";
        }
        if (array_key_exists('modes_id', $data) and $data['modes_id'] != "") {
            $query_array[":modes_id"] = $data['modes_id'];
            $form_query['modes_id'] = ":modes_id";
        }
        $values = http_build_query($form_query);
        $values = str_replace("%3A", ":", $values);
        $values = str_replace("&", ", ", $values);
        //error_log('UPDATE chat SET '.$values.' WHERE chat.id = :chat_id');
        #$values = implode(', ', array_keys($query_array));
        #$into = str_replace(":", "", $values);
        $query = 'UPDATE chat SET '.$values.' WHERE chat.id = :chat_id';
        return $this->queryFetchAll($query, $query_array);
    }

    public function Insert($data){
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
        //error_log(json_encode($query_array), 3, "post.log");
        $values = implode(', ', array_keys($query_array));
        $into = str_replace(":", "", $values);
        $query = 'INSERT INTO chat (' . $into . ') VALUES (' . $values . ')';
        return $this->queryFetchAll($query, $query_array);
    }

    public function login($data){
        $query_array = array(":username"=>$data["username"]);
        $query = 'SELECT password FROM user WHERE user.username = :username';
        return $this->queryFetchAll($query, $query_array);
    }

    private function queryFetchAll( $query, $queryParams ) {
        //$this->logger->logEvent("query: ".$query, __FILE__, __LINE__, __FUNCTION__);
        //$this->logger->logEvent("params: ".var_export($queryParams, true), __FILE__, __LINE__, __FUNCTION__);
        // подготовка запроса
        error_log("query: ".$query. __FILE__. __LINE__. __FUNCTION__, 3, MODULES_DIR."bd.log");
        error_log("params: ".var_export($queryParams, true). __FILE__. __LINE__. __FUNCTION__, 3, MODULES_DIR."bd.log");

        $sth = $this->dbh->prepare( $query );
        // выполнение запроса
        try {
            $sth->execute($queryParams);
            $res = $sth->fetchAll();
        } catch (\PDOException $e){
            error_log($e, 3, "bd.log");
            $res = 1;
        }
        return $res;
    }
}