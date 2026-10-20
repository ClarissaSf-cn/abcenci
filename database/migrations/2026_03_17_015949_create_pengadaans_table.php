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
        Schema::create('pengadaans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stationery_id')->constrained('stationeries')->onDelete('cascade');

            $table->string('no_pr');
            $table->date('tgl_pr');

            $table->date('estimasi_tiba')->nullable();

            $table->date('tgl_released_pr')->nullable();

            $table->string('no_po')->nullable();
            $table->integer('qty_item');
            $table->integer('qty_po')->nullable();

            $table->enum('status', ['Menunggu', 'Selesai'])->default('Menunggu');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengadaans');
    }
};
