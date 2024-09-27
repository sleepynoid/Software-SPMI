<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\PelaksanaanController;
use App\Http\Controllers\PeningkatanController;
use App\Http\Controllers\SheetController;
use App\Http\Controllers\testcontroller;
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

Route::post('/submitPelaksanaan', [SheetController::class, 'submitPelaksanaan'])->middleware('auth:sanctum');
Route::post('/submitEvaluasi', [EvaluasiController::class, 'submitEval'])->middleware('auth:sanctum');
Route::post('/submitPengendalian', [PengendalianController::class, 'submitPengendalian'])->middleware('auth:sanctum');
Route::post('/submitPeningkatan', [PeningkatanController::class, 'submitPeningkatan'])->middleware('auth:sanctum');

Route::get('/buktipelaksanaan',[PelaksanaanController::class,'getComment']);
Route::post('/buktipelaksanaan',[PelaksanaanController::class, 'postComment'])->middleware('auth:sanctum');

Route::get('/getLink/{idBukti}/{tipeLink}',[PelaksanaanController::class, 'getLink']);
Route::post('/submitLink',[PelaksanaanController::class,'postLink'])->middleware('auth:sanctum');
Route::post('/deleteLink',[PelaksanaanController::class,'deleteLink'])->middleware('auth:sanctum');

Route::post('/submitComment',[PelaksanaanController::class,'postComment']);
Route::post('/deleteComment',[PelaksanaanController::class,'delComment']);
//Route::get('/sheed', [PenetapanController::class, 'index']);

Route::get('/admin/listuser', [AccountController::class, 'listUser']);
Route::post('/admin/register', [AccountController::class, 'register']);
Route::post('/admin/edit/role', [AccountController::class, 'editUserRole']);