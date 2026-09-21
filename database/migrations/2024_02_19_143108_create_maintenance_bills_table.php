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
        Schema::create('maintenance_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenances_id')->nullable(); // ID del mantenimiento, bien sea de motor, caja, cámara
            $table->string('multi_tools')->nullable(); // Herramientas Usadas en el proceso del mantenimiento
            $table->string('multi_equipament')->nullable(); // Equipo usado tipo montacargas, gruas, etc (varchar(255))
            $table->string('mechanic')->nullable(); // Comisión del mecánico (varchar(255))
            $table->string('mechanic_assistant')->nullable(); // comisión para el asistente de mecánica
            $table->string('seller')->nullable(); // Comisión del vendedor
            $table->string('seller_assistant')->nullable(); // Comisión del vendedor
            $table->string('cleaning')->nullable(); // Gastos de desengrasante, jabòn liquido
            $table->string('drinking_water')->nullable(); // Gastos de Agua Potable
            $table->string('consumables')->nullable(); // Gastos de consumibles usados
            $table->string('camera_technician')->nullable(); // Comisiòn del Tècnico de Càmara (varchar(255))
            $table->string('camera_technical_assistant')->nullable(); // Comisiòn del Ayudante Tècnico de Càmara
            $table->string('forklift_driver')->nullable(); // Comisión del Montacarguista
            //$table->string('programmer'); // Comisión del mantenimiento del sistema y creación de mejoras 
            $table->timestamps();
            $table->foreign('maintenances_id')->references('id')->on('maintenances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_bills');
    }
};
