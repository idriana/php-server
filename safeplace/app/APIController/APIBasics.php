<?php

namespace app\APIController;

class APIBasics
{
    protected function CheckMethod($method){
        $m = $_SERVER['REQUEST_METHOD'];
        if ($m != $method) {
            //http_response_code(400);
            //error_log('deleteitem2: неверный метод');
            return false;
        }
        return true;
    }

    protected function CheckValues($params, $data){
        $res = true;
        if (!isset($data))
            foreach ($params as $param)
                if(!array_key_exists($params, $data) or empty($data[$param])) {
                    //http_response_code(400);
                    //error_log('deleteitem2: недостаточно данных');
                    //$header = "Location: ".'http://'.$_SERVER['HTTP_HOST']."/src/deleteitem.php";
                    //$header = $header."?".http_build_query(array("error"=>true, "error_text"=>"Заполните поля, отмеченные *"));
                    //header($header);
                    //die('{"status": "error", "message": "Failed to delete record"}');
                    $res = false;
            }
        return $res;
    }

    protected function redirect($url, $params=[]){
        $header = "Location: ".'http://'.$_SERVER['HTTP_HOST'].$url;
        $header = $header."?".http_build_query($params);
        header($header);
        die('redirect');
    }

    protected function ecr($data){
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value);
        }
        return $data;
    }
}