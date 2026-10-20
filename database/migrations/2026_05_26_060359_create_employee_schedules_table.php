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
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->comment('Merujuk pada NIK Karyawan');
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->date('schedule_date')->comment('Tanggal penugasan shift');
            $table->timestamps();

            $table->unique(['nik', 'schedule_date'], 'emp_schedule_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_schedules');
    }
};
