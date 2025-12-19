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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->date('fecha'); //Fecha en que se realizó el mantenimiento (date)
            $table->text('descripcion'); //Descripción breve del mantenimiento realizado 
            $table->string('tipo'); //Preventivo, Correctivo, Inspección, otro
            $table->enum("estado", ["EN ESPERA", "EN PROCESO", "TERMINADO", "NO SE PUDO CONTINUAR"]); // Estado del mantenimiento (enum):
            $table->unsignedBigInteger('partida_id'); // ID del motor, caja, cámara
            $table->integer('cedula_mecanico'); // Cédula del mécanico valor numérico de 6-8 dígitos
            $table->string('nombre_mecanico'); // Nombre del mecánico (varchar(255))
            $table->string('apellido_mecanico'); // Apellido del mecánico (varchar(255))
            $table->string('observaciones')->nullable(); // Observaciones del mantenimiento en co de cualquier eventualidad, campo opcional (varchar(255))
            $table->timestamps();
            $table->foreign('partida_id')->references('id')->on('partidas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
