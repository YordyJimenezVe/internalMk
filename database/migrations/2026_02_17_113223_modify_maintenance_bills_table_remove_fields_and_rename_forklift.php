<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maintenance_bills', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_bills', 'multi_equipament')) {
                $table->dropColumn('multi_equipament');
            }
            if (Schema::hasColumn('maintenance_bills', 'drinking_water')) {
                $table->dropColumn('drinking_water');
            }
            if (Schema::hasColumn('maintenance_bills', 'forklift_driver')) {
                // If forklift already exists (partial migration), we just drop the old one
                if (Schema::hasColumn('maintenance_bills', 'forklift')) {
                    $table->dropColumn('forklift_driver');
                } else {
                    $table->string('forklift')->nullable()->after('forklift_driver');
                    $table->dropColumn('forklift_driver');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_bills', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenance_bills', 'multi_equipament')) {
                $table->string('multi_equipament')->after('multi_tools')->nullable();
            }
            if (!Schema::hasColumn('maintenance_bills', 'drinking_water')) {
                $table->string('drinking_water')->after('cleaning')->nullable();
            }
            if (Schema::hasColumn('maintenance_bills', 'forklift')) {
                $table->renameColumn('forklift', 'forklift_driver');
            }
        });
    }
};
