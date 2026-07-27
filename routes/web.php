<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/ms-login', function () {
    return view('ms-login');
})->name('ms-login');

