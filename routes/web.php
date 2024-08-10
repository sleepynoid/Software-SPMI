<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\PenetapanController;
use App\Http\Controllers\testcontroller;
use App\Http\Controllers\JsonController;
use Illuminate\Support\Facades\Route;

//Route::get('/', [JsonController::class, 'GetSheet']);
Route::view('/','welcome');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');