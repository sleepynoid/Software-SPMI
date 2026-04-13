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
        Schema::create('evaluasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sheet');
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->string('submitted_by')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_sheet')->references('id')->on('sheets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasis');
    }
};
