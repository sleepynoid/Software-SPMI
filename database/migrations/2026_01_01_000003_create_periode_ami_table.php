<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_ami', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_akademik');
            $table->date('tgl_mulai_audit');
            $table->date('tgl_selesai_audit');
            $table->enum('status', ['Draft', 'Pelaksanaan EDOM', 'Audit Lapangan', 'RTM', 'Selesai']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_ami');
    }
};
