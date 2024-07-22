<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JsonController;
use App\Http\Controllers\PenetapanController;

Route::get('/sheet',[JsonController::class, 'index']);



//Route::get('/sheed', [PenetapanController::class, 'index']);
