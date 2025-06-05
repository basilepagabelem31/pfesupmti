<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StagiaireConrtoller extends Controller
{
    //
    public function index ()
    {
        return view('stagiaires.dashboard');
    }
}
