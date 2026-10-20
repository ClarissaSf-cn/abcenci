<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('tanggal');
            $table->boolean('is_libur')->default(false);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('istirahat', 8, 2)->default(0);
            $table->decimal('jumlah_jam', 8, 2);

            $table->decimal('jam_1_5', 8, 2)->default(0);
            $table->decimal('jam_2', 8, 2)->default(0);
            $table->decimal('jam_3', 8, 2)->default(0);
            $table->decimal('jam_4', 8, 2)->default(0);

            $table->decimal('total_akumulasi_lembur', 8, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lembur');
    }
};
