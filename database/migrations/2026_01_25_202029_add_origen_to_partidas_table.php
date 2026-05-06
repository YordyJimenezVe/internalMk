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
            $table->string('origen')->default('IMPORTADO')->after('id'); // IMPORTADO or NACIONAL

            // Also ensure columns are nullable if they weren't already (though we did a previous migration for some)
            // container_id should be nullable now for NACIONAL items.
            // We already made it nullable in 'make_partida_columns_nullable' but let's double check logic won't fail.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropColumn('origen');
        });
    }
};
