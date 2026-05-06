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
        Schema::create('permission_expirations', function (Blueprint $table) {
            $table->id();
            $table->morphs('model'); // model_type, model_id (User/Role)
            $table->string('permission_name');
            $table->timestamp('expires_at');
            $table->timestamps();

            // Index for quick lookup of expired permissions
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_expirations');
    }
};
