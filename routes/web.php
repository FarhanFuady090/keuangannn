<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing-page');
});

Route::permanentRedirect('/login', '/')->name('login');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/logout')->name('logout');

require __DIR__ . '/auth.php';
require __DIR__ . '/auth-admin.php';
require __DIR__ . '/auth-tupusat.php';
require __DIR__ . '/auth-tuunit.php';
require __DIR__ . '/auth-yayasan.php';
require __DIR__ . '/admin-auth.php';
require __DIR__ . '/tupusat-auth.php';
require __DIR__ . '/tuunit-auth.php';
require __DIR__ . '/yayasan-auth.php';