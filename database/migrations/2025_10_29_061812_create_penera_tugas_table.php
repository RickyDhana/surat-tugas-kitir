<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penera_tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_tugas_id');
            $table->string('nama_penera', 100);
            $table->string('nip', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->string('realisasi_jam_orang')->nullable();
            $table->date('realisasi_mulai')->nullable();
            $table->date('realisasi_selesai')->nullable();

            // 🔁 Kolom realisasi B1–B10
            for ($i = 1; $i <= 10; $i++) {
                foreach (['c1', 'c2', 'r1', 'r2', 'd1', 'd2'] as $suffix) {
                    $table->string("realisasi_b{$i}_{$suffix}", 10)->nullable();
                }
            }

            $table->timestamps();
            $table->foreign('surat_tugas_id')->references('id')->on('surat_tugas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penera_tugas');
    }
};
