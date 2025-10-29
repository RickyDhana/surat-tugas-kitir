<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kitir_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kitir_id');
            $table->integer('step_no'); // 1–7
            $table->enum('posisi', ['Y1', 'Y2']);
            $table->date('tanggal')->nullable();
            $table->time('waktu')->nullable();
            $table->string('paraf')->nullable(); // nama user
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('kitir_id')->references('id')->on('kitirs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('kitir_steps');
    }
};
