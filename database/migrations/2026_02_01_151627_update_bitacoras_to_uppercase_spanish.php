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
        // Actualizar registros existentes a MAYÚSCULAS
        DB::table('bitacoras')->update([
            'action' => DB::raw('UPPER(action)'),
            'description' => DB::raw('UPPER(description)')
        ]);

        // Traducir acciones comunes si están en inglés (opcional pero recomendado para consistencia)
        DB::table('bitacoras')->where('action', 'like', 'CREATED%')->update(['action' => DB::raw("REPLACE(action, 'CREATED', 'CREADO')")]);
        DB::table('bitacoras')->where('action', 'like', 'UPDATED%')->update(['action' => DB::raw("REPLACE(action, 'UPDATED', 'ACTUALIZADO')")]);
        DB::table('bitacoras')->where('action', 'like', 'DELETED%')->update(['action' => DB::raw("REPLACE(action, 'DELETED', 'ELIMINADO')")]);
        DB::table('bitacoras')->where('action', 'like', 'RESTORED%')->update(['action' => DB::raw("REPLACE(action, 'RESTORED', 'RESTAURADO')")]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir a minúsculas
    }
};
