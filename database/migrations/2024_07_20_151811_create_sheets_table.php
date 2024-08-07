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
            $table->string('jurusan');
            $table->string('periode');
            $table->longText('note');
            $table->enum('tipe_sheet', ['pendidikan', 'pengabdian', 'penelitian']);
            // $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('sheets');
    }
};
