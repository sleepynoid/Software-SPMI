<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', [JsonController::class, 'GetSheet']);
Route::view('/','welcome');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');