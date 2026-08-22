<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standar_dikti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('periode_id');
            $table->unsignedBigInteger('kategori_id');
            $table->string('nama_standar');
            $table->timestamps();

            $table->foreign('periode_id')->references('id')->on('periode_ami')->onDelete('cascade');
            $table->foreign('kategori_id')->references('id')->on('kategori_standar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standar_dikti');
    }
};
