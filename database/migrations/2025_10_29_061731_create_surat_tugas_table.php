<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->date('tanggal')->nullable();
            $table->string('uraian_pekerjaan')->nullable();
            $table->string('rencana_jam_orang')->nullable();
            $table->string('rencana_mulai')->nullable();
            $table->string('rencana_selesai')->nullable();
            $table->string('kabiro_kalibrasi')->default('Dwi Adi');
            $table->enum('status', ['Draft', 'Proses', 'Selesai'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_tugas');
    }
};
