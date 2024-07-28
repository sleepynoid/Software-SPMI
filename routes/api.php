<?php

use App\Http\Controllers\testcontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JsonController;
use App\Http\Controllers\PenetapanController;


Route::apiResource('/sheet', JsonController::class);
Route::apiResource('/penetapan',PenetapanController::class);
Route::apiResource('/testing',testcontroller::class);

//Route::get('/sheed', [PenetapanController::class, 'index']);
