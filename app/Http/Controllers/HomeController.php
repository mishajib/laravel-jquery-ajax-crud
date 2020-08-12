<?php

namespace App\Http\Controllers;

use App\Todo;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $todos = Todo::latest()->get();
        return view('home', compact('todos'));
    }
}
