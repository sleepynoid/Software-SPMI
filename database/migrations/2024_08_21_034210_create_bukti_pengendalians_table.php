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
        Schema::create('bukti_pengendalians', function (Blueprint $table) {
            $table->id();
            $table->string('temuan');
            $table->string('akar_masalah');
            $table->string('rtl');
            $table->string('pelaksanaan_rtl');
            $table->unsignedBigInteger('id_bukti_evaluasi');
            $table->timestamps();

            $table->foreign('id_bukti_evaluasi')->references('id')->on('bukti_evaluasis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_pengendalians');
    }
};
