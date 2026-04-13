<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AccountController::class, 'loginForm'])->name('login');
    Route::post('/login', [AccountController::class, 'login']);
    Route::get('/register', [AccountController::class, 'registerForm'])->name('register');
    Route::post('/register', [AccountController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');
    
    Route::get('/', function () {
        $sheets = app(\App\Http\Controllers\SheetController::class)->getAllSheetForInertia();
        return \Inertia\Inertia::render('home', ['sheets' => $sheets]);
    })->name('home');

    Route::get('/sheet/{jurusan}/{periode}/{tipeSheet}/{step?}', [App\Http\Controllers\SheetController::class, 'show'])->name('sheet.show');

    Route::post('/submitPelaksanaan', [\App\Http\Controllers\PelaksanaanController::class, 'submitPelaksanaan']);
    Route::post('/submitEvaluasi', [\App\Http\Controllers\EvaluasiController::class, 'submitEval']);
    Route::post('/submitPengendalian', [\App\Http\Controllers\PengendalianController::class, 'submitPengendalian']);
    Route::post('/submitPeningkatan', [\App\Http\Controllers\PeningkatanController::class, 'submitPeningkatan']);

    // Admin routes
    Route::get('/admin/users', [\App\Http\Controllers\AccountController::class, 'listUser'])->name('admin.users');
    Route::post('/admin/users', [\App\Http\Controllers\AccountController::class, 'register'])->name('admin.users.store');
    Route::post('/admin/users/role', [\App\Http\Controllers\AccountController::class, 'editUserRole'])->name('admin.users.role');
    Route::post('/admin/users/password', [\App\Http\Controllers\AccountController::class, 'resetPassword'])->name('admin.users.password');
    Route::delete('/admin/users/{id}', [\App\Http\Controllers\AccountController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/admin/api-logs', [\App\Http\Controllers\ApiLogController::class, 'getUserHistory'])->name('admin.logs');
});