<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;


class navigationControl extends Controller
{
    //view about html
    public function about(Request $request): View
    {
        return view('about');
    }
    //IoT controller
    public function control(Request $request): View
    {
        return view('optionNavigation.control');
    }
    //IoT controller
    public function parameter(Request $request): View
    {
        return view('optionNavigation.parameter');
    }
}
