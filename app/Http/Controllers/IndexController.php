<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

class IndexController extends Controller
{
  public function __construct(){
      header("Content-Type: text/html");
  }
    /**
     * Show the profile for the given user.
     *
     * @param  int $id
     * @return Response
     */
    public function getIndex()
    {
        return View::make('index');
    }

}
