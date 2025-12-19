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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenances_id')->nullable(); // ID del mantenimiento, bien sea de motor, caja, cámara
            $table->string('concha_biela')->nullable(); // Precio en divisa de concha de biela
            $table->string('concha_bancada')->nullable(); //  Precio en divisa de concha de Bancada
            $table->string('anillos')->nullable(); //  Precio en divisa de concha de
            $table->string('empacadura_camara')->nullable(); //  Precio en divisa empacadura
            $table->string('empacadura_carter')->nullable(); //  Precio en divisa empacadura
            $table->string('kit_empacaduras')->nullable(); //  Precio en divisa kit empacadura
            $table->string('baño_quimico')->nullable(); //  Precio en divisa para un servicio de baño quimico
            $table->string('goma_valvula')->nullable(); //  Precio en divisa de gomas valvula
            $table->string('planos')->nullable(); //  Precio en divisa de planos
            $table->string('valvulas')->nullable(); //  Precio en divisa de válvulas
            $table->string('rectificadora')->nullable(); //  Precio en divisa que cobra la rectificadora por su servicio
            $table->string('asientos')->nullable(); //  Precio en divisa de los asientos de càmara
            $table->string('camisas')->nullable(); //  Precio en divisa de las camisas de la cámara
            $table->string('levas')->nullable(); //  Precio en divisa de las levas
            $table->string('pistones')->nullable(); // Precio en divisa de los pistones del motor
            $table->timestamps();
            $table->foreign('maintenances_id')->references('id')->on('maintenances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
