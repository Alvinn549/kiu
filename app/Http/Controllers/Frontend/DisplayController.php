<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class DisplayController extends Controller
{
    public function index()
    {
        return view('frontend.display.index');
    }
}
