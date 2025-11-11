<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KitirController;
use App\Http\Controllers\SuratTugasController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ================= KITIR =================
    Route::prefix('kitir')->name('kitir.')->group(function () {
        Route::get('/', [KitirController::class, 'index'])->name('index');
        Route::post('/store', [KitirController::class, 'store'])->name('store');
        Route::get('/{id}', [KitirController::class, 'show'])->name('show');
        Route::get('/{id}/preview', [KitirController::class, 'preview'])->name('preview');
        Route::get('/{id}/download', [KitirController::class, 'downloadPdf'])->name('download');
        Route::get('/{id}/edit', [KitirController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KitirController::class, 'update'])->name('update');
        Route::delete('/{id}', [KitirController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/step', [KitirController::class, 'storeStep'])->name('step.store');
        Route::post('/{id}/catatan', [KitirController::class, 'storeCatatan'])->name('catatan.store');
    });

    // ================= SURAT TUGAS =================
    Route::prefix('surat_tugas')->name('surat_tugas.')->group(function () {
        Route::get('/', [SuratTugasController::class, 'index'])->name('index');
        Route::get('/create', [SuratTugasController::class, 'create'])->name('create');
        Route::post('/store', [SuratTugasController::class, 'store'])->name('store');
        Route::get('/{id}', [SuratTugasController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SuratTugasController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SuratTugasController::class, 'update'])->name('update');
        Route::delete('/{id}', [SuratTugasController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/preview', [SuratTugasController::class, 'preview'])->name('preview');
        Route::get('/{id}/downloadPdf', [SuratTugasController::class, 'downloadPdf'])->name('downloadPdf');
        Route::put('/{id}/realisasi', [SuratTugasController::class, 'realisasiUpdate'])->name('realisasi.update');
    });

});
