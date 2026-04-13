<?php

use App\Http\Controllers\PelaksanaanController;
use App\Http\Controllers\SheetController;
use App\Http\Controllers\PenetapanController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/penetapan/import', [PenetapanController::class, 'import']);
    Route::get('/downloadSheet', [SheetController::class, 'downloadExcel']);
    Route::get('/getAllSheet', [SheetController::class, 'getAllSheet']);

    Route::get('/getLink/{idBukti}/{tipeLink}', [PelaksanaanController::class, 'getLink']);
    Route::post('/submitLink', [PelaksanaanController::class, 'postLink']);
    Route::post('/deleteLink', [PelaksanaanController::class, 'deleteLink']);
});
