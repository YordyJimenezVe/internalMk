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
        Schema::create('maintenance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_id')->constrained('maintenances')->onDelete('cascade');
            $table->string('description');
            $table->decimal('cost', 10, 2);
            $table->string('invoice_path')->nullable(); // Capture/Photo
            $table->enum('type', ['REPUESTO', 'SERVICIO']); // Repuesto = Biela, etc. Servicio = Baño quimico, etc.
            $table->timestamps();
        });

        Schema::create('maintenance_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_id')->constrained('maintenances')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // If internal user
            $table->string('external_name')->nullable(); // If external person
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_teams');
        Schema::dropIfExists('maintenance_items');
    }
};
