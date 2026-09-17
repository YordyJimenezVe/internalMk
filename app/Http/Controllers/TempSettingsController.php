<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TempSettingsController extends Controller
{
    /**
     * Actualiza la tasa BCV y/o % de utilidad temporales en la sesión del usuario administrador.
     */
    public function update(Request $request)
    {
        $tasa = $request->input('tasa_bcv');
        $utilidad = $request->input('utilidad');

        if ($tasa !== null && $tasa !== '') {
            $cleanTasa = str_replace(',', '.', str_replace(' ', '', $tasa));
            $parsedTasa = (float) $cleanTasa;
            if ($parsedTasa > 0) {
                $request->session()->put('temp_tasa_bcv', $parsedTasa);
            }
        } else {
            $request->session()->forget('temp_tasa_bcv');
        }

        if ($utilidad !== null && $utilidad !== '') {
            $parsedUtil = (float) $utilidad;
            if ($parsedUtil >= 0) {
                $request->session()->put('temp_utilidad', $parsedUtil);
            }
        } else {
            $request->session()->forget('temp_utilidad');
        }

        return redirect()->back()->with('success', 'Ajustes temporales de edición aplicados correctamente.');
    }

    /**
     * Restablece con 1 solo clic la tasa BCV oficial del día y la utilidad predeterminada.
     */
    public function reset(Request $request)
    {
        $request->session()->forget(['temp_tasa_bcv', 'temp_utilidad']);
        return redirect()->back()->with('success', 'Se ha restablecido la Tasa BCV oficial del día y la utilidad predeterminada.');
    }
}
