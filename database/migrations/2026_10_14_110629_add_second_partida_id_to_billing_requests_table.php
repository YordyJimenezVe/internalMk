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
        Schema::table('billing_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('second_partida_id')->nullable()->after('partida_id');
            $table->foreign('second_partida_id')->references('id')->on('inventarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_requests', function (Blueprint $table) {
            $table->dropForeign(['second_partida_id']);
            $table->dropColumn('second_partida_id');
        });
    }
};