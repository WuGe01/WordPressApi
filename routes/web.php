<?php

use App\Http\Controllers\WordPressController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/create-user', [WordPressController::class, 'createUser'])->name('create-user');

Route::post('/update-user-password', [WordPressController::class, 'updateUserPassword'])->name('update-user-password');