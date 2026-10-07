<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function index()
    {
        return view('Layouts.index');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function registro()
    {
        return view('auth.register');
    }
}