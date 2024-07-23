<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JsonController;
use App\Http\Controllers\PenetapanController;

Route::get('/sheet',[JsonController::class, 'index']);
Route::get('/standar',[JsonController::class, 'getStandard']);
Route::get('/indikator',[JsonController::class, 'getIndicator']);
Route::get('/target',[JsonController::class, 'getTarget']);



//Route::get('/sheed', [PenetapanController::class, 'index']);
