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
        // Update statuses in maintenances table
        Schema::table('maintenances', function (Blueprint $table) {
            $table->enum('status', ["EN ESPERA", "EN PROCESO", "TERMINADO", "CULMINADO", "CANCELADO"])->change();
        });

        // We'll keep the columns in maintenance_bills but the logic will group them in the UI.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->enum('status', ["EN ESPERA", "EN PROCESO", "TERMINADO", "NO SE PUDO CONTINUAR"])->change();
        });
    }
};
