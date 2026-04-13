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
        Schema::create('bukti_evaluasis', function (Blueprint $table) {
            $table->id();
            $table->enum('adjustment',['melampai','mencapai','belum mencapai','menyimpan']);
            $table->text('komentar');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('edited_by')->nullable();
            $table->unsignedBigInteger('id_evaluasi');
            $table->unsignedBigInteger('id_bukti_pelaksanaan');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('id_evaluasi')->references('id')->on('evaluasis')->onDelete('cascade');
            $table->foreign('id_bukti_pelaksanaan')->references('id')->on('bukti_pelaksanaans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_evaluasis');
    }
};
