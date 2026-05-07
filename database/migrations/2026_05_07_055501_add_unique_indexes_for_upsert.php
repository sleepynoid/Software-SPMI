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
        Schema::table('indikator_mutu', function (Blueprint $table) {
            $table->unique(['standar_id', 'kode_indikator'], 'indikator_standar_kode_unique');
        });

        Schema::table('target_unit', function (Blueprint $table) {
            $table->unique(['indikator_id', 'unit_kerja_id'], 'target_indikator_unit_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator_mutu', function (Blueprint $table) {
            $table->dropUnique('indikator_standar_kode_unique');
        });

        Schema::table('target_unit', function (Blueprint $table) {
            $table->dropUnique('target_indikator_unit_unique');
        });
    }
};
