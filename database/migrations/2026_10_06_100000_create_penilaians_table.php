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
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->string('bulan', 50);
            $table->string('tahun', 10);
            $table->string('nik', 50)->index();
            $table->string('employee_name')->nullable();
            $table->string('user')->nullable();
            $table->string('vendor_os')->nullable();
            $table->string('position_text')->nullable();
            $table->string('pers_subarea_text')->nullable();
            $table->integer('skor_disiplin')->default(0);
            $table->integer('skor_kualitas')->default(0);
            $table->integer('skor_produktivitas')->default(0);
            $table->integer('skor_kerjasama')->default(0);
            $table->integer('skor_tanggung_jawab')->default(0);
            $table->json('aspek_scores')->nullable();
            $table->integer('total_skor')->default(0);
            $table->string('dipertahankan', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['nik', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
