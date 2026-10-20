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
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->string('schedule_type', 30)->default('Reguler')->after('schedule_date')->comment('Jenis jadwal: Reguler, Jadwal Khusus, Tidak Normal, Per Tanggal');
            $table->text('special_note')->nullable()->after('schedule_type')->comment('Catatan khusus untuk jadwal tanggal tertentu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->dropColumn(['schedule_type', 'special_note']);
        });
    }
};
