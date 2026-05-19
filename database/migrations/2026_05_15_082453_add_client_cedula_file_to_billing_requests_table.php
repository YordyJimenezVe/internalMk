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
            $table->string('client_cedula_file')->nullable()->after('client_cedula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_requests', function (Blueprint $table) {
            $table->dropColumn('client_cedula_file');
        });
    }
};
