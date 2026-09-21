<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            $table->dropColumn(['costo_importacion', 'costo_nacionalizacion']);
        });

        Schema::table('containers', function (Blueprint $table) {
            $table->decimal('costo_importacion_general', 15, 2)->default(0)->after('accesorios');
        });
    }

    public function down(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            $table->dropColumn('costo_importacion_general');
        });
    }
};
