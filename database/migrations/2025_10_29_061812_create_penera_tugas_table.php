<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('penera_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_tugas_id')->constrained('surat_tugas')->onDelete('cascade');

            for ($i = 1; $i <= 10; $i++) {
                $table->string("B{$i}_C1")->nullable();
                $table->string("B{$i}_C2")->nullable();
                $table->string("B{$i}_R1")->nullable();
                $table->string("B{$i}_R2")->nullable();
                $table->string("B{$i}_D1")->nullable();
                $table->string("B{$i}_D2")->nullable();
            }

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penera_tugas');
    }
};
