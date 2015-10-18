<?php

namespace App\Http\Controllers;
use App\User;
use App\Http\Controllers\Controller;

//use Illuminate\Support\Facades\DB;
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
    }

}