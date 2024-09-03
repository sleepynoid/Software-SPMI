<?php

use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\PelaksanaanController;
use App\Http\Controllers\SheetController;
use App\Http\Controllers\testcontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenetapanController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PengendalianController;

Route::apiResource('/penetapan',PenetapanController::class);
Route::apiResource('/testing',testcontroller::class);
Route::post('/login',[AccountController::class,'login']);
Route::post('/register', [AccountController::class, 'register']);
// Route::middleware('auth:sanctum')->post('/logout', [AccountController::class, 'logout']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AccountController::class, 'logout']);
});

Route::post('/penetapan/import',[PenetapanController::class,'import']);
Route::get('/downloadSheet', [SheetController::class, 'downloadExcel']);

Route::get('/getPenetapan/{jurusan}/{periode}/{tipePendidikan}/{tipe}', [SheetController::class, 'getPenetapan']);
Route::get('/getPeriode/{jurusan}', [SheetController::class, 'getPeriode']);

Route::post('/submitPelaksanaan', [SheetController::class, 'submitPelaksanaan']);
Route::post('/submitEvaluasi', [EvaluasiController::class, 'submitEval']);
Route::post('/submitPengendalian', [PengendalianController::class, 'submitPengendalian']);

Route::get('/buktipelaksanaan',[PelaksanaanController::class,'getComment']);
Route::post('/buktipelaksanaan',[PelaksanaanController::class, 'postComment']);

Route::get('/getLink/{idBukti}/{tipeLink}',[PelaksanaanController::class, 'getLink']);
Route::post('/submitLink',[PelaksanaanController::class,'postLink']);
Route::post('/deleteLink',[PelaksanaanController::class,'deleteLink']);

Route::post('/submitComment',[PelaksanaanController::class,'postComment']);
Route::post('/deleteComment',[PelaksanaanController::class,'delComment']);
//Route::get('/sheed', [PenetapanController::class, 'index']);

Route::get('/admin/listuser', [AccountController::class, 'listUser']);

