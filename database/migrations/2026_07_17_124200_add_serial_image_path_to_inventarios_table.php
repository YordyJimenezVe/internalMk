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
        if (!Schema::hasColumn('inventarios', 'serial_image_path')) {
            Schema::table('inventarios', function (Blueprint $table) {
                $table->string('serial_image_path')->nullable()->after('serial');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('inventarios', 'serial_image_path')) {
            Schema::table('inventarios', function (Blueprint $table) {
                $table->dropColumn('serial_image_path');
            });
        }
    }
};
