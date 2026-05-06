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
        if (!Schema::hasTable('bitacoras')) {
            Schema::create('bitacoras', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('users_id');
                $table->string('action');//DELETE, INSERT, UPDATE, VIEW
                $table->string('description');// THE USER YORDY HAS BEN DELETE THE FACTURE 12 BECAUSE ERROR TRANSITION
                $table->timestamps();
                $table->foreign('users_id')->references('id')->on('users')->onDelete('no action');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
