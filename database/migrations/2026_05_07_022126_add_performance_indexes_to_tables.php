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
        Schema::table('target_unit', function (Blueprint $table) {
            $table->index(['unit_kerja_id', 'indikator_id']);
        });
        Schema::table('kertas_kerja_audit', function (Blueprint $table) {
            $table->index(['capaian_id', 'kategori_temuan'], 'kka_capaian_temuan_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_unit', function (Blueprint $table) {
            $table->dropIndex(['unit_kerja_id', 'indikator_id']);
        });
        Schema::table('kertas_kerja_audit', function (Blueprint $table) {
            $table->dropIndex('kka_capaian_temuan_idx');
        });
    }
};
