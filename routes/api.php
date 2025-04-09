<?php

//use Illuminate\Routing\Controller;
use App\Http\Controllers\ApiLogController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\PelaksanaanController;
use App\Http\Controllers\PeningkatanController;
use App\Http\Controllers\SheetController;
use App\Http\Controllers\testcontroller;
use App\Http\Middleware\LogApiRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenetapanController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PengendalianController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AccountController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/penetapan/import', [PenetapanController::class, 'import']);
    Route::get('/downloadSheet', [SheetController::class, 'downloadExcel']);

    Route::get('/getPelaksanaan/{jurusan}/{periode}/{tipePendidikan}/{tipe}', [PelaksanaanController::class, 'getPelaksanaan']);
    Route::get('/getEvaluasi/{jurusan}/{periode}/{tipePendidikan}/{tipe}', [EvaluasiController::class, 'getEvaluasi']);
    Route::get('/getPengendalian/{jurusan}/{periode}/{tipePendidikan}/{tipe}', [PengendalianController::class, 'getPengendalian']);
    Route::get('/getPeningkatan/{jurusan}/{periode}/{tipePendidikan}/{tipe}', [PeningkatanController::class, 'getPeningkatan']);

    Route::get('/getPeriode/{jurusan}', [SheetController::class, 'getPeriode']);
    Route::get('/getAllSheet', [SheetController::class, 'getAllSheet']);
    Route::middleware([LogApiRequests::class])->group(function () {
        Route::post('/submitPelaksanaan', [PelaksanaanController::class, 'submitPelaksanaan']);
        Route::post('/submitEvaluasi', [EvaluasiController::class, 'submitEval']);
        Route::post('/submitPengendalian', [PengendalianController::class, 'submitPengendalian']);
        Route::post('/submitPeningkatan', [PeningkatanController::class, 'submitPeningkatan']);
    });

    Route::get('/getLink/{idBukti}/{tipeLink}', [PelaksanaanController::class, 'getLink']);
    Route::post('/submitLink', [PelaksanaanController::class, 'postLink']);
    Route::post('/deleteLink', [PelaksanaanController::class, 'deleteLink']);

    Route::get('/admin/listuser', [AccountController::class, 'listUser']);
    Route::post('/admin/registerUser', [AccountController::class, 'register']);
    Route::post('/admin/edit/role', [AccountController::class, 'editUserRole']);
    Route::post('/admin/reset-password', [AccountController::class, 'resetPassword']);
    Route::get('/api-logs', [ApiLogController::class, 'index']);
    Route::post('/api-logs-user', [ApiLogController::class, 'getUserHistory']);
});

Route::post('/login', [AccountController::class, 'login']);
Route::post('/register', [AccountController::class, 'register']);


