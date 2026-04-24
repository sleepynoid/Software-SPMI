<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capaian_pelaksanaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_unit_id');
            $table->float('nilai_aktual')->nullable();
            $table->text('evaluasi_diri')->nullable();
            $table->string('link_dokumen_bukti')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('target_unit_id')->references('id')->on('target_unit')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capaian_pelaksanaan');
    }
};
