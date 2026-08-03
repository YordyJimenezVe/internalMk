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
        if (!Schema::hasTable('maintenance_status_logs')) {
            Schema::create('maintenance_status_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('maintenance_id');
                $table->string('status');
                $table->string('photo_path')->nullable();
                $table->timestamps();

                $table->foreign('maintenance_id')->references('id')->on('maintenances')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_status_logs');
    }
};
