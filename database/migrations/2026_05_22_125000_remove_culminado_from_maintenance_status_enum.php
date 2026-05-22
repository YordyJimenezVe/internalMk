<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing records to TERMINADO if they are CULMINADO
        DB::table('maintenances')
            ->where('status', 'CULMINADO')
            ->update(['status' => 'TERMINADO']);

        // Now change the enum on the table to exclude CULMINADO
        Schema::table('maintenances', function (Blueprint $table) {
            $table->enum('status', ["EN ESPERA", "EN PROCESO", "TERMINADO", "CANCELADO"])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->enum('status', ["EN ESPERA", "EN PROCESO", "TERMINADO", "CULMINADO", "CANCELADO"])->change();
        });
    }
};
