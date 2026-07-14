<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_requests', function (Blueprint $table) {
            $table->string('client_email')->nullable()->after('client_address');
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->string('client_email')->nullable()->after('client_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_requests', function (Blueprint $table) {
            $table->dropColumn('client_email');
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('client_email');
        });
    }
};
