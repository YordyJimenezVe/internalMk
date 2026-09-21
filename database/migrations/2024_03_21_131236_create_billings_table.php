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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partida_id')->nullable(); // ID del mantenimiento, bien sea de motor, caja, cámara
            $table->string('big')->nullable();  // Comisión del mecánico (varchar(255))
            $table->string('iva')->nullable();  // Comisión del mecánico (varchar(255))
            $table->string('bs')->nullable();  // Comisión del mecánico (varchar(255))
            $table->string('value_divisa')->nullable();  // Comisión del mecánico (varchar(255))
            $table->string('divisa')->nullable();  // Comisión del mecánico (varchar(255))
            $table->string('precio_total')->nullable();  // Comisión del mecánico (varchar(255))
            $table->string('igtf')->nullable(); // Comisión del vendedor
            $table->date('fecha')->nullable(); // Precio en divisa de concha de biela
            $table->time('hora')->nullable(); //  Precio en divisa de concha de Bancada
            $table->string('numero_factura')->nullable(); // Gastos de desengrasante, jabòn liquido
            $table->string('numero_control')->nullable(); // Gastos de desengrasante, jabòn liquido
            $table->string('numero_nota_credito')->nullable(); // Gastos de desengrasante, jabòn liquido
            $table->string('numero_factura_afect')->nullable(); // Gastos de desengrasante, jabòn liquido
            $table->unsignedBigInteger('user_id')->nullable(); // ID del mantenimiento, bien sea de motor, caja, cámara
            $table->timestamps();
            $table->foreign('partida_id')->references('id')->on('partidas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
