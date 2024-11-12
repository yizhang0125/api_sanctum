<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [ViewController::class,'register']);
Route::get('/login', [ViewController::class, 'login'])->name('login');

// Dashboard route, protected by authentication
Route::get('/dashboard', [ViewController::class, 'dashboard'])->name('dashboard');
Route::post('logout', [ViewController::class, 'logout'])->name('logout');
