<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pesanan')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('nomor_kt', 50)->nullable();
            $table->string('uraian_pekerjaan', 250)->nullable();
            $table->string('rencana_jam_orang', 50)->nullable();
            $table->string('rencana_mulai', 50)->nullable();
            $table->string('rencana_selesai', 50)->nullable();
            $table->string('kabiro_kalibrasi', 100)->default('Dwi Adi');
            $table->string('form_code', 50)->nullable();
            $table->string('status', 20)->default('Draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_tugas');
    }
};
