<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;


class Controller extends BaseController
{
    public function __construct(){
        header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE");
        header("Content-Type: application/json");
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
}
