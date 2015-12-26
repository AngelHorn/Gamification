<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;


class Controller extends BaseController
{
    static $http_codes = array();

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE");
        header("Content-Type: application/json");
//        $this->sessionFilter();
    }

    public function export($code, $data = "", $message = "")
    {
        echo json_encode(array(
            "code" => $code,
            "data" => $data,
            "message" => $message,
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    public function sessionFilter()
    {
        if (Session::has('account')) {
            if (Session::get('login-date') != date("Ymd")) {
                $this->export(413);
                die;
            }
        } else {
            Session::set('account', array('uid'=>1));
            Session::set('login-date', date("Ymd"));
        }
    }


}
