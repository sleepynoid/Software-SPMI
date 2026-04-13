<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jurusan');
            $table->string('periode');
            $table->longText('note')->nullable();
            $table->enum('tipe_sheet', ['pendidikan', 'pengabdian', 'penelitian']);
            // $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            $table->foreign('id_jurusan')->references('id')->on('jurusans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('sheets');
    }
};
