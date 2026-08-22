<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjut_ptk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kka_id');
            $table->text('akar_masalah')->nullable();
            $table->text('rencana_tindak_lanjut')->nullable();
            $table->date('jadwal_penyelesaian')->nullable();
            $table->enum('status_verifikasi', ['Open', 'Menunggu Verifikasi', 'Closed'])->default('Open');
            $table->timestamps();

            $table->foreign('kka_id')->references('id')->on('kertas_kerja_audit')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut_ptk');
    }
};
