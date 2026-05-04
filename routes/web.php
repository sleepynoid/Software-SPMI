<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master;
use App\Http\Controllers\Penetapan;
use App\Http\Controllers\Pelaksanaan;
use App\Http\Controllers\Evaluasi;
use App\Http\Controllers\Pengendalian;
use App\Http\Controllers\Peningkatan;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AccountController::class, 'loginForm'])->name('login');
    Route::post('/login', [AccountController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');
    
    Route::get('/', function() { return redirect('/dashboard'); });
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Master Data (Admin/LPM only)
    Route::middleware('role:Admin/LPM')->prefix('master')->name('master.')->group(function () {
        Route::resource('users', Master\UserController::class);
        Route::resource('unit-kerja', Master\UnitKerjaController::class);
        Route::resource('kategori-standar', Master\KategoriStandarController::class);
    });

    // Penetapan (Admin/LPM only)
    Route::middleware('role:Admin/LPM')->prefix('penetapan')->name('penetapan.')->group(function () {
        Route::resource('periode', Penetapan\PeriodeController::class);
        Route::resource('standar', Penetapan\StandarController::class);
        Route::resource('indikator', Penetapan\IndikatorMutuController::class);
        
        Route::get('/distribusi-target', [Penetapan\DistribusiTargetController::class, 'index'])->name('distribusi-target.index');
        Route::post('/distribusi-target', [Penetapan\DistribusiTargetController::class, 'store'])->name('distribusi-target.store');
    });

    // Pelaksanaan (Auditee only)
    Route::middleware('role:Auditee')->prefix('pelaksanaan')->name('pelaksanaan.')->group(function () {
        Route::get('/evaluasi-diri', [Pelaksanaan\EvaluasiDiriController::class, 'index'])->name('evaluasi-diri.index');
        Route::post('/evaluasi-diri/{targetUnit}', [Pelaksanaan\EvaluasiDiriController::class, 'store'])->name('evaluasi-diri.store');
    });

    // Evaluasi / AMI (Auditor only)
    Route::middleware('role:Auditor')->prefix('evaluasi')->name('evaluasi.')->group(function () {
        Route::get('/jadwal-audit', [Evaluasi\JadwalAuditController::class, 'index'])->name('jadwal-audit.index');
        Route::get('/kka/{unit}', [Evaluasi\KKAController::class, 'show'])->name('kka.show');
        Route::post('/kka/{capaian}', [Evaluasi\KKAController::class, 'store'])->name('kka.store');
    });

    // Pengendalian / RTL (Auditee only)
    Route::middleware('role:Auditee')->prefix('pengendalian')->name('pengendalian.')->group(function () {
        Route::get('/isi-rtl', [Pengendalian\RtlController::class, 'index'])->name('isi-rtl.index');
        Route::post('/isi-rtl/{kka}', [Pengendalian\RtlController::class, 'store'])->name('isi-rtl.store');
    });

    // Peningkatan / RTM (Pimpinan only)
    Route::middleware('role:Pimpinan')->prefix('peningkatan')->name('peningkatan.')->group(function () {
        Route::get('/risalah', [Peningkatan\RisalahRtmController::class, 'index'])->name('risalah.index');
        Route::post('/risalah', [Peningkatan\RisalahRtmController::class, 'store'])->name('risalah.store');
        Route::put('/risalah/{risalah}', [Peningkatan\RisalahRtmController::class, 'update'])->name('risalah.update');
        Route::delete('/risalah/{risalah}', [Peningkatan\RisalahRtmController::class, 'destroy'])->name('risalah.destroy');
    });
});