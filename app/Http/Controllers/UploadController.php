<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UploadController extends Controller
{

    /**
     * @return Response
     */
    public function postIndex()
    {
        if (!Request::hasFile('item_img')) {
            $this->export(50000);
            die;
        }
        $destinationPath = base_path() . '/public/assets/uploads';
        $fileName = md5(microtime()) . '.' .
            Request::file('item_img')->getClientOriginalExtension();
        $isMoved = Request::file('item_img')->move($destinationPath, $fileName);
        if ($isMoved) {
            $this->export(200, $fileName);
        } else {
            $this->export(50000);
        }

    }
}
