<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bukti_pelaksanaans', function (Blueprint $table) {
            $table->id();
            $table->longText('komentar');
            $table->unsignedBigInteger('id_pelaksanaan');
            $table->unsignedBigInteger('id_indikator');
            $table->timestamps();

            $table->foreign('id_pelaksanaan')->references('id')->on('pelaksanaans')->onDelete('cascade');
            $table->foreign('id_indikator')->references('id')->on('indikators')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_pelaksanaans');
    }
};
