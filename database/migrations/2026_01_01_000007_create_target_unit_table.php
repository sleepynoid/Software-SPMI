<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_unit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indikator_id');
            $table->unsignedBigInteger('unit_kerja_id');
            $table->float('nilai_target');
            $table->string('satuan');
            $table->timestamps();

            $table->foreign('indikator_id')->references('id')->on('indikator_mutu')->onDelete('cascade');
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerja')->onDelete('cascade');
            $table->unique(['indikator_id', 'unit_kerja_id'], 'target_indikator_unit_unique');
            $table->index(['unit_kerja_id', 'indikator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_unit');
    }
};
