<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JsonController;
use App\Http\Controllers\PenetapanController;

Route::apiResource('/sheet', [JsonController::class]);



//Route::get('/sheed', [PenetapanController::class, 'index']);
