<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kitirs', function (Blueprint $table) {
            $table->id();
            $table->string('cal_request_no');
            $table->date('tgl_penyelesaian')->nullable();
            $table->enum('status', ['draft', 'in_progress', 'selesai'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('kitirs');
    }
};
