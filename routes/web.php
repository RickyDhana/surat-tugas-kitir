<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KitirController;
use App\Http\Controllers\SuratTugasController;

// ===============================
// ROUTE AUTENTIKASI
// ===============================
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===============================
// ROUTE DENGAN MIDDLEWARE AUTH
// ===============================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===============================
    // MODUL KITIR
    // ===============================
    Route::get('/kitir', [KitirController::class, 'index'])->name('kitir.index');
    Route::post('/kitir/store', [KitirController::class, 'store'])->name('kitir.store');
    Route::get('/kitir/{id}', [KitirController::class, 'show'])->name('kitir.show');
    Route::get('/kitir/{id}/preview', [KitirController::class, 'preview'])->name('kitir.preview');
    Route::get('/kitir/{id}/download', [KitirController::class, 'downloadPdf'])->name('kitir.download');
    Route::get('/kitir/{id}/edit', [KitirController::class, 'edit'])->name('kitir.edit');
    Route::put('/kitir/{id}', [KitirController::class, 'update'])->name('kitir.update');
    Route::delete('/kitir/{id}', [KitirController::class, 'destroy'])->name('kitir.destroy');

    // Step & Catatan
    Route::post('/kitir/{id}/step', [KitirController::class, 'storeStep'])->name('kitir.step.store');
    Route::post('/kitir/{id}/catatan', [KitirController::class, 'storeCatatan'])->name('kitir.catatan.store');

    // ===============================
    // SURAT TUGAS - ROLE CONTROL
    // ===============================

    // ✅ Khusus role Kepala Biro => CRUD + PDF
    Route::middleware(['role:kepala_biro'])->group(function () {
        Route::get('surat_tugas/create', [SuratTugasController::class, 'create'])->name('surat_tugas.create');
        Route::post('surat_tugas/store', [SuratTugasController::class, 'store'])->name('surat_tugas.store');
        Route::get('surat_tugas/{id}/edit', [SuratTugasController::class, 'edit'])->name('surat_tugas.edit');
        Route::put('surat_tugas/{id}', [SuratTugasController::class, 'update'])->name('surat_tugas.update');
        Route::delete('surat_tugas/{id}', [SuratTugasController::class, 'destroy'])->name('surat_tugas.destroy');
        Route::get('surat_tugas/{id}/download-pdf', [SuratTugasController::class, 'downloadPdf'])->name('surat_tugas.downloadPdf');
    });

    // ✅ Semua role bisa lihat list dan preview
    Route::get('surat_tugas', [SuratTugasController::class, 'index'])->name('surat_tugas.index');
    Route::get('surat_tugas/{id}', [SuratTugasController::class, 'show'])->name('surat_tugas.show');
    Route::get('surat_tugas/{id}/preview', [SuratTugasController::class, 'preview'])->name('surat_tugas.preview');
});
