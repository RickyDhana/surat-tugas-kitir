<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('catatan_kitir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kitir_id');
            $table->unsignedBigInteger('user_id');
            $table->string('isi_catatan', 255);
            $table->timestamps();

            $table->foreign('kitir_id')->references('id')->on('kitirs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('catatan_kitir');
    }
};
