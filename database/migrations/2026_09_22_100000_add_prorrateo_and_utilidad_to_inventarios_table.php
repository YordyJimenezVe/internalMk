<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            if (!Schema::hasColumn('inventarios', 'prorrateo_gastos')) {
                $table->decimal('prorrateo_gastos', 15, 2)->default(0.00)->after('costo_importacion_unitario');
            }
            if (!Schema::hasColumn('inventarios', 'porcentaje_utilidad')) {
                $table->decimal('porcentaje_utilidad', 5, 2)->nullable()->after('prorrateo_gastos');
            }
        });

        // Update items belonging to expediente 260723
        // Tasa BCV: 736.933
        // FOB Bs: 160,651.39 ($218.00 USD)
        // Prorrateo Gastos Bs: 96,196.71
        // Costo Landed: 256,848.10 Bs.
        // Utilidad: 10%
        // Base Imponible (B.I.G.): 282,532.91 Bs.
        // IVA 16%: 45,205.27 Bs.
        // Precio Final con IVA Bs: 327,738.17 Bs.
        // Precio Venta Comercial USD: 444.73 USD
        DB::table('inventarios')
            ->where('expediente', '260723')
            ->update([
                'costo' => 218.00,
                'costo_importacion_unitario' => 160651.39,
                'prorrateo_gastos' => 96196.71,
                'porcentaje_utilidad' => 10.00,
                'price' => 282532.91,
                'price_sale' => 444.73,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            if (Schema::hasColumn('inventarios', 'prorrateo_gastos')) {
                $table->dropColumn('prorrateo_gastos');
            }
            if (Schema::hasColumn('inventarios', 'porcentaje_utilidad')) {
                $table->dropColumn('porcentaje_utilidad');
            }
        });
    }
};
