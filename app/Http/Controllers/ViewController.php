<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        return view('welcome');
    }
    public function register()
    {
        return view('register');
    }
    public function login()
    {
        return view('login');       
    }
    public function dashboard()
    {
        return view('dashboard');   
    }
    public function logout(Request $request)
{
    auth()->logout();
    return redirect('/login');
}

}
