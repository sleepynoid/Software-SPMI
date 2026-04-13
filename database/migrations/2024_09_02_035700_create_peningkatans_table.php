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
        Schema::create('peningkatans', function (Blueprint $table) {
            $table->id();
            $table->text('komentar');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('edited_by')->nullable();
            $table->unsignedBigInteger('id_pengendalian');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('id_pengendalian')->references('id')->on('bukti_pengendalians')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peningkatans');
    }
};
