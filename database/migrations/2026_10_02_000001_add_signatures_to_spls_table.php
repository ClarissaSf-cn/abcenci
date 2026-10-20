<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('spls', function (Blueprint $table) {
            $table->string('dibuat_oleh')->nullable()->after('created_by');
            $table->string('ditugaskan_oleh')->nullable()->after('dibuat_oleh');
            $table->string('disetujui_oleh')->nullable()->after('ditugaskan_oleh');
            $table->string('diketahui_oleh')->nullable()->after('disetujui_oleh');
        });
    }

    public function down(): void {
        Schema::table('spls', function (Blueprint $table) {
            $table->dropColumn(['dibuat_oleh', 'ditugaskan_oleh', 'disetujui_oleh', 'diketahui_oleh']);
        });
    }
};
