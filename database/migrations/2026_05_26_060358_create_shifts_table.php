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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('wst_code', 10)->unique();
            $table->string('wst_text', 50)->nullable();
            $table->time('time_start');
            $table->time('time_end');
            $table->boolean('is_overnight')->default(false);

            $table->time('break1_start')->nullable();
            $table->time('break1_end')->nullable();
            $table->integer('break1_duration')->default(0);

            $table->time('break2_start')->nullable();
            $table->time('break2_end')->nullable();
            $table->integer('break2_duration')->default(0);

            $table->string('group_ws_type', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
