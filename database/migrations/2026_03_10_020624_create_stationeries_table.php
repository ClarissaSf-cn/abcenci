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
        Schema::create('stationeries', function (Blueprint $table) {
            $table->id();

            $table->string('kode')->unique();
            $table->string('nama_barang');
            $table->string('kategori')->nullable();
            $table->string('satuan');

            $table->integer('stok_awal')->default(0);
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);
            $table->integer('harga_unit')->default(0);

            $table->decimal('avg_penggunaan', 8, 2)->default(0);
            $table->integer('lead_time')->default(0);
            $table->integer('safety_stock')->default(0);

            $table->integer('min_stok')->default(5);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stationeries');
    }
};
