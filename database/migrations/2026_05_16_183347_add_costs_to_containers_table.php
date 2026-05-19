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
        Schema::table('containers', function (Blueprint $table) {
            $table->decimal('costo_importacion', 15, 2)->default(0)->after('accesorios');
            $table->decimal('costo_nacionalizacion', 15, 2)->default(0)->after('costo_importacion');
            $table->boolean('aplicar_costos')->default(false)->after('costo_nacionalizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            $table->dropColumn(['costo_importacion', 'costo_nacionalizacion', 'aplicar_costos']);
        });
    }
};
