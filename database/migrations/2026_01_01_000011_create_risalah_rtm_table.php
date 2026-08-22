<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risalah_rtm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('periode_id');
            $table->unsignedBigInteger('unit_kerja_id');
            $table->date('tgl_rtm');
            $table->string('pimpinan_rapat');
            $table->text('isi_risalah');
            $table->text('keputusan_peningkatan');
            $table->timestamps();

            $table->foreign('periode_id')->references('id')->on('periode_ami')->onDelete('cascade');
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerja')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risalah_rtm');
    }
};
