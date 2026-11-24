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
        if (!Schema::hasColumn('billings', 'warranty_months')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->unsignedTinyInteger('warranty_months')->nullable()->after('observaciones');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('billings', 'warranty_months')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropColumn('warranty_months');
            });
        }
    }
};
