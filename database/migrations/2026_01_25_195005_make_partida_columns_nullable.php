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
        Schema::table('partidas', function (Blueprint $table) {
            $table->string('marca')->nullable()->change();
            $table->string('modelo')->nullable()->change();
            $table->string('año')->nullable()->change();
            $table->string('codInv')->nullable()->change();
            $table->string('expediente')->nullable()->change();
            $table->string('condicion')->nullable()->change();
            // Container ID is separate constraint, assuming we keep it nullable or handle it.
            // Original migration had: $table->unsignedBigInteger('container_id');
            // If we want it nullable:
            $table->unsignedBigInteger('container_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to not null is risky if we added nulls. 
        // We generally don't revert this strictly or we define defaults.
        Schema::table('partidas', function (Blueprint $table) {
            $table->string('marca')->nullable(false)->change();
            // ... etc
        });
    }
};
