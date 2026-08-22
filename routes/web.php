<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Evaluasi\JadwalAuditController;
use App\Http\Controllers\Evaluasi\KKAController;
use App\Http\Controllers\Master\KategoriStandarController;
use App\Http\Controllers\Master\UnitKerjaController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Pelaksanaan\EvaluasiDiriController;
use App\Http\Controllers\Penetapan\DistribusiTargetController;
use App\Http\Controllers\Penetapan\ImportStandarController;
use App\Http\Controllers\Penetapan\IndikatorMutuController;
use App\Http\Controllers\Penetapan\PeriodeController;
use App\Http\Controllers\Penetapan\StandarController;
use App\Http\Controllers\Pengendalian\RtlController;
use App\Http\Controllers\Peningkatan\RisalahRtmController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Master Data (Admin/LPM only)
    Route::middleware('role:Admin/LPM')->prefix('master')->name('master.')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'edit', 'create']);
        Route::resource('unit-kerja', UnitKerjaController::class)->except(['show', 'edit', 'create']);
        Route::resource('kategori-standar', KategoriStandarController::class)->except(['show', 'edit', 'create']);
    });

    // Penetapan (Admin/LPM only)
    Route::middleware('role:Admin/LPM')->prefix('penetapan')->name('penetapan.')->group(function () {
        Route::resource('periode', PeriodeController::class)->except(['show', 'edit', 'create']);
        Route::resource('standar', StandarController::class)->except(['show', 'edit', 'create']);
        Route::get('/import-standar', [ImportStandarController::class, 'index'])->name('standar.import.index');
        Route::post('/import-standar', [ImportStandarController::class, 'store'])->name('standar.import.store');
        Route::resource('indikator', IndikatorMutuController::class)->except(['show', 'edit', 'create']);
        Route::get('/distribusi-target', [DistribusiTargetController::class, 'index'])->name('distribusi-target.index');
        Route::post('/distribusi-target', [DistribusiTargetController::class, 'store'])->name('distribusi-target.store');
    });

    // Pelaksanaan (Auditee only)
    Route::middleware('role:Auditee')->prefix('pelaksanaan')->name('pelaksanaan.')->group(function () {
        Route::get('/evaluasi-diri', [EvaluasiDiriController::class, 'index'])->name('evaluasi-diri.index');
        Route::post('/evaluasi-diri/{targetUnit}', [EvaluasiDiriController::class, 'store'])->name('evaluasi-diri.store');
    });

    // Evaluasi (Auditor only)
    Route::middleware('role:Auditor')->prefix('evaluasi')->name('evaluasi.')->group(function () {
        Route::get('/jadwal-audit', [JadwalAuditController::class, 'index'])->name('jadwal-audit.index');
        Route::get('/kka/{unit}', [KKAController::class, 'show'])->name('kka.show');
        Route::post('/kka/{capaian}', [KKAController::class, 'store'])->name('kka.store');
    });

    // Pengendalian (Auditee only)
    Route::middleware('role:Auditee')->prefix('pengendalian')->name('pengendalian.')->group(function () {
        Route::get('/isi-rtl', [RtlController::class, 'index'])->name('isi-rtl.index');
        Route::post('/isi-rtl/{kka}', [RtlController::class, 'store'])->name('isi-rtl.store');
    });

    // Peningkatan (Pimpinan only)
    Route::middleware('role:Pimpinan')->prefix('peningkatan')->name('peningkatan.')->group(function () {
        Route::get('/risalah', [RisalahRtmController::class, 'index'])->name('risalah.index');
        Route::post('/risalah', [RisalahRtmController::class, 'store'])->name('risalah.store');
        Route::put('/risalah/{risalah}', [RisalahRtmController::class, 'update'])->name('risalah.update');
        Route::delete('/risalah/{risalah}', [RisalahRtmController::class, 'destroy'])->name('risalah.destroy');
    });
});

require __DIR__.'/settings.php';
