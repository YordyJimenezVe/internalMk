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
        Schema::table('maintenance_items', function (Blueprint $table) {
            $table->string('source')->default('INVENTARIO')->after('type'); // INVENTARIO or COMPRADO
            $table->string('document_type')->default('NINGUNO')->after('source'); // FACTURA, RECIBO, NINGUNO
            $table->string('invoice_number')->nullable()->after('document_type'); // Vendor invoice number
            $table->decimal('base_imponible', 12, 2)->nullable()->after('invoice_number'); // BIG (Base Imponible)
            $table->string('status')->default('COMPLETADO')->after('base_imponible'); // COMPLETADO, FUERA, RETORNADO, CONCILIADO
            $table->date('outflow_date')->nullable()->after('status'); // Date sent to machine shop
            $table->date('return_date')->nullable()->after('outflow_date'); // Date returned
            $table->text('notes')->nullable()->after('return_date'); // Additional notes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_items', function (Blueprint $table) {
            $table->dropColumn([
                'source',
                'document_type',
                'invoice_number',
                'base_imponible',
                'status',
                'outflow_date',
                'return_date',
                'notes',
            ]);
        });
    }
};
