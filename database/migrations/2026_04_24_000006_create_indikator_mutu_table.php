<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikator_mutu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('standar_id');
            $table->string('kode_indikator');
            $table->text('isi_standar');
            $table->enum('jenis', ['IKU', 'IKT']);
            $table->timestamps();

            $table->foreign('standar_id')->references('id')->on('standar_dikti')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikator_mutu');
    }
};
