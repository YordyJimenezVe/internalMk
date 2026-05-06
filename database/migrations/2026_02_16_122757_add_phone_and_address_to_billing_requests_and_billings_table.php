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
        Schema::table('billing_requests', function (Blueprint $table) {
            $table->string('client_phone')->nullable()->after('client_cedula');
            $table->text('client_address')->nullable()->after('client_phone');
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->string('client_phone')->nullable()->after('client_cedula');
            $table->text('client_address')->nullable()->after('client_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_requests', function (Blueprint $table) {
            $table->dropColumn(['client_phone', 'client_address']);
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn(['client_phone', 'client_address']);
        });
    }
};
