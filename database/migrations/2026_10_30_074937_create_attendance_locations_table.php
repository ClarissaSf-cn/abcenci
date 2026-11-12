<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_locations', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('radius_meter')->default(100);
            $table->text('alamat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert initial default location (PT Indofood CBP Sukses Makmur Tbk)
        DB::table('attendance_locations')->insert([
            'nama_lokasi' => 'Pabrik PT Indofood CBP Sukses Makmur Tbk',
            'latitude' => -3.5489500,
            'longitude' => 114.7298100,
            'radius_meter' => 100,
            'alamat' => 'Jl. A. Yani KM. 32, Loktabat, Banjarbaru, Kalimantan Selatan',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_locations');
    }
};
