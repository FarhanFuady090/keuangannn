<?php

use App\Http\Controllers\yayasan\Auth\LoginController;
use App\Http\Controllers\yayasan\LapSiswaController;
use App\Http\Controllers\yayasan\LapKasController;
use App\Http\Controllers\yayasan\LapTabunganController;
use App\Http\Controllers\yayasan\DashboardController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::prefix('yayasan')->middleware('guest:yayasan')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('yayasan.login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::prefix('yayasan')->middleware('auth:yayasan', 'verified')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('yayasan.dashboard.index');
    Route::get('/export-excel', [DashboardController::class, 'exportExcel'])->name('dashboard.export.excel');
    Route::get('/export-pdf', [DashboardController::class, 'exportPDF'])->name('dashboard.export.pdf');

    Route::prefix('laporan')->middleware('auth')->group(function () {
        Route::prefix('siswa')->middleware('auth')->group(function () {
            Route::get('/', [LapSiswaController::class, 'IndexSiswa'])->name('yayasan.laporan.siswa.index');
            Route::get('/detail-data-siswa/{id}', [LapSiswaController::class, 'ShowSiswa'])->name('yayasan.laporan.siswa.show');
        });

        Route::prefix('kas')->middleware('auth')->group(function () {
            Route::get('/', [LapKasController::class, 'index'])->name('yayasan.laporan.kas.index');
            Route::get('/trashed', [LapKasController::class, 'trashed'])->name('yayasan.laporan.kas.trashed');
        });
        Route::prefix('tabungan')->middleware('auth')->group(function () {
            Route::get('/', [LapTabunganController::class, 'index'])->name('yayasan.laporan.tabungan.index');
            Route::get('/{id}/export-pdf', [LapTabunganController::class, 'exportPdf'])->name('yayasan.laporan.tabungan.export.pdf');
            // Export Excel seluruh tabungan
            Route::get('/export', [LapTabunganController::class, 'exportAll'])->name('yayasan.laporan.tabungan.export.all');
            Route::get('/{id}', [LapTabunganController::class, 'show'])->name('yayasan.laporan.tabungan.show');
        });
    });

    Route::post('logout', [LoginController::class, 'destroy'])->name('yayasan.logout');
});
