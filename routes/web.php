<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AccountController::class, 'loginForm'])->name('login');
    Route::post('/login', [AccountController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');
    
    Route::get('/', function () {
        return \Inertia\Inertia::render('home');
    })->name('home');

    Route::get('/sheet/{jurusan}/{periode}/{tipeSheet}/{step?}', [App\Http\Controllers\SheetController::class, 'show'])->name('sheet.show');

    Route::post('/submitPelaksanaan', [\App\Http\Controllers\PelaksanaanController::class, 'submitPelaksanaan']);
    Route::post('/submitEvaluasi', [\App\Http\Controllers\EvaluasiController::class, 'submitEval']);
    Route::post('/submitPengendalian', [\App\Http\Controllers\PengendalianController::class, 'submitPengendalian']);
    Route::post('/submitPeningkatan', [\App\Http\Controllers\PeningkatanController::class, 'submitPeningkatan']);
});