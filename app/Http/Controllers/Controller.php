<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;


class Controller extends BaseController
{
    public function __construct(){
        // header('Access-Control-Allow-Credentials:true');
        // header("Access-Control-Allow-Methods: POST,GET,OPTIONS,DELETE");
        // header("Access-Control-Allow-Headers: X-Requested-With");
        header("Access-Control-Allow-Origin: *");
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
