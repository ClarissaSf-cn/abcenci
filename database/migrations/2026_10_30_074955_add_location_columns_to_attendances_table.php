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
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('catatan');
            }
            if (!Schema::hasColumn('attendances', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('attendances', 'jarak_meter')) {
                $table->integer('jarak_meter')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('attendances', 'lokasi_nama')) {
                $table->string('lokasi_nama')->nullable()->after('jarak_meter');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('attendances', 'latitude')) $columnsToDrop[] = 'latitude';
            if (Schema::hasColumn('attendances', 'longitude')) $columnsToDrop[] = 'longitude';
            if (Schema::hasColumn('attendances', 'jarak_meter')) $columnsToDrop[] = 'jarak_meter';
            if (Schema::hasColumn('attendances', 'lokasi_nama')) $columnsToDrop[] = 'lokasi_nama';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
