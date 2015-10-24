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
        $fuck = DB::table('quests')->get();
        var_dump($fuck);
        return View::make('fuck');
    }

}