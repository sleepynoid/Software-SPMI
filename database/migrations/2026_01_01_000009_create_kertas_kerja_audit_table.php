<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kertas_kerja_audit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('capaian_id');
            $table->unsignedBigInteger('auditor_id');
            $table->enum('kategori_temuan', ['Sesuai', 'Melampaui', 'Observasi (OB)', 'KTS Minor', 'KTS Mayor']);
            $table->text('deskripsi_temuan')->nullable();
            $table->timestamps();

            $table->foreign('capaian_id')->references('id')->on('capaian_pelaksanaan')->onDelete('cascade');
            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['capaian_id', 'kategori_temuan'], 'kka_capaian_temuan_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kertas_kerja_audit');
    }
};
