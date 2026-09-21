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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('cedula'); // Cédula valor numérico de 6-8 dígitos
            $table->string('nombre'); // Nombre (varchar(255))
            $table->string('apellido'); // Apellido (varchar(255))
            $table->string('tlf')->nullable(); // tlf (varchar(255))
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
