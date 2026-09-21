<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For container 260723 (rate 736.933):
        // FOB / Literal import price: $218.00 USD -> 160,651.39 Bs.
        // Base Imponible (B.I.G.): 282,532.91 Bs. ($383.39 USD)
        // Precio Final con IVA: 327,738.18 Bs. ($444.73 USD)
        DB::table('inventarios')
            ->where('expediente', '260723')
            ->update([
                'costo' => 218.00,
                'costo_importacion_unitario' => 160651.39,
                'price' => 282532.91
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('inventarios')
            ->where('expediente', '260723')
            ->update([
                'costo_importacion_unitario' => 282532.91
            ]);
    }
};
