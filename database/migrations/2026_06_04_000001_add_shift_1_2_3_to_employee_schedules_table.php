<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {

            $table->foreignId('shift_1_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('shift_2_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('shift_3_id')->nullable()->constrained('shifts')->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->dropForeign(['shift_1_id']);
            $table->dropForeign(['shift_2_id']);
            $table->dropForeign(['shift_3_id']);

            $table->dropColumn(['shift_1_id', 'shift_2_id', 'shift_3_id']);
        });
    }
};

