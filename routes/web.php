<?php

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
})->name('login');

Route::get('auth/{provider}', [SocialAuthController::class, 'redirect'])->name('redirect');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('callback');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});