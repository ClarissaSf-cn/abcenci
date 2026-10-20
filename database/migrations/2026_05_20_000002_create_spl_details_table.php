<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('spl_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spl_id')->constrained('spls')->onDelete('cascade');
            $table->string('nik')->nullable();
            $table->string('nama')->nullable();
            $table->string('jabatan')->nullable();
            $table->time('jam_in')->nullable();
            $table->time('jam_out')->nullable();
            $table->time('lembur_awal')->nullable();
            $table->time('lembur_akhir')->nullable();
            $table->time('istirahat_awal')->nullable();
            $table->time('istirahat_akhir')->nullable();
            $table->decimal('total_jam', 8, 2)->default(0);
            $table->string('alasan')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('spl_details');
    }
};
