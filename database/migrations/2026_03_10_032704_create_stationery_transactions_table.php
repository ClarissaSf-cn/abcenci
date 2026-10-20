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
        Schema::create('stationery_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stationery_id')->constrained('stationeries')->onDelete('cascade');

            $table->enum('type', ['masuk', 'keluar']);

            $table->integer('jumlah');
            $table->date('tanggal');

            $table->string('no_fpb')->nullable();
            $table->string('dept')->nullable();
            $table->string('keterangan')->nullable();

            $table->string('no_dokumen')->nullable();
            $table->string('vendor')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stationery_transactions');
    }
};
