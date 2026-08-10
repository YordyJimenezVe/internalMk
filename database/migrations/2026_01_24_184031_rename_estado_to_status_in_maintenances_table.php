<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE maintenances CHANGE estado status ENUM('EN ESPERA','EN PROCESO','TERMINADO','NO SE PUDO CONTINUAR') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE maintenances CHANGE status estado ENUM('EN ESPERA','EN PROCESO','TERMINADO','NO SE PUDO CONTINUAR') NOT NULL");
        }
    }
};
