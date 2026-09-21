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
        Schema::create('accesorio_engines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenances_id'); // ID del mantenimiento, bien sea de motor, caja, cámara
            $table->string('valve_cover')->nullable(); // Precio en divisa de concha de biela
            $table->string('chain_cover')->nullable(); //  Precio en divisa de concha de Bancada
            $table->string('carter')->nullable(); //  Precio en divisa de concha de
            $table->string('pescador')->nullable(); //  Precio en divisa empacadura
            $table->timestamps();
            $table->foreign('maintenances_id')->references('id')->on('maintenances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accesorio_engines');
    }
};
