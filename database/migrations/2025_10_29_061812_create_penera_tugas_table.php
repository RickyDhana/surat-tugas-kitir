<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penera_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_tugas_id')
                ->constrained('surat_tugas')
                ->onDelete('cascade');

            $table->string('nama_penera', 100);
            $table->string('nip', 50)->nullable();
            $table->text('catatan')->nullable();

            // 📅 Realisasi tanggal B1–B10 (semua penera bisa isi)
            for ($i = 1; $i <= 10; $i++) {
                $table->date("realisasi_tgl_b{$i}")->nullable();
            }

            // 🧾 Realisasi nilai (C, R, D) masing-masing penera
            // C untuk Pak Candra, R untuk Pak Rizqi, D untuk Pak Rino
            for ($i = 1; $i <= 10; $i++) {
                foreach (['c1', 'c2', 'r1', 'r2', 'd1', 'd2'] as $suffix) {
                    $table->string("realisasi_b{$i}_{$suffix}", 10)->nullable();
                }
            }

            // 🕓 Data umum realisasi
            $table->string('realisasi_jam_orang')->nullable();
            $table->date('realisasi_mulai')->nullable();
            $table->date('realisasi_selesai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penera_tugas');
    }
};