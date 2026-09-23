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
        Schema::table('billing_requests', function (Blueprint $table) {
            $table->decimal('tasa_bcv', 15, 4)->nullable()->after('price');
            $table->date('fecha_tasa_bcv')->nullable()->after('tasa_bcv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_requests', function (Blueprint $table) {
            $table->dropColumn(['tasa_bcv', 'fecha_tasa_bcv']);
        });
    }
};
