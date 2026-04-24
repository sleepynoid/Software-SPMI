<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit');
            $table->enum('jenis_unit', ['Fakultas', 'Program Studi', 'Biro', 'Lembaga']);
            $table->unsignedBigInteger('kepala_unit_id')->nullable();
            $table->timestamps();

            // Foreign key to users will be added later or kept as is if nullable
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_kerja');
    }
};
