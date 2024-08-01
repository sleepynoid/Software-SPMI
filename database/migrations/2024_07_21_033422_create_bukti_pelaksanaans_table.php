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
        Schema::create('bukti_pelaksanaans', function (Blueprint $table) {
            $table->id();
            $table->string('judul_link');
            $table->string('link');
            $table->unsignedBigInteger('id_pelaksanaan');
            $table->timestamps();

            $table->foreign('id_pelaksanaan')->references('id')->on('pelaksanaans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_pelaksanaans');
    }
};
