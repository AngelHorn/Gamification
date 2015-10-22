<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{

    /**
     * Show the profile for the given user.
     *
     * @param  int $id
     * @return Response
     */
    public function getIndex()
    {
        $fuck = DB::table('f_chuzheng')->get();
        var_dump($fuck);
        echo "fuck" . '<br>';
        return View::make('fuck');
    }

}