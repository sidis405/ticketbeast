<?php

namespace App\Http\Controllers;

use App\Concert;

class ConcertsController extends Controller
{
    public function show(Concert $concert)
    {
        return view('concerts.show')->with(compact('concert'));
    }
}
