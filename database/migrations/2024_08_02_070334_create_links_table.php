<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Reference\Reference;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('judul_link');
            $table->string('link');
            // $table->unsignedBigInteger('id_bukti_pelaksanaan');
            $table->enum('tipe_link',['bukti_pelaksanaan','bukti_evaluasi']);
            $table->unsignedBigInteger('id_bukti');
            $table->timestamps();

            // $table->foreign('id_bukti_pelaksanaan')->references('id')->on('bukti_pelaksanaans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
