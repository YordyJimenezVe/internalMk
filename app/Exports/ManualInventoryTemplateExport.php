<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;

class ManualInventoryTemplateExport implements FromView, WithEvents, ShouldAutoSize, WithColumnWidths
{
    public function view(): View
    {
        return view('exports.manual_inventory_template');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // N°
            'B' => 18,  // COD INVENTARIO
            'C' => 22,  // EXPEDIENTE
            'D' => 20,  // TIPO PRODUCTO
            'E' => 18,  // MARCA
            'F' => 28,  // MODELO
            'G' => 10,  // AÑO
            'H' => 22,  // SERIAL
            'I' => 12,  // CANTIDAD
            'J' => 18,  // CONDICION
            'K' => 14,  // COSTO USD
            'L' => 20,  // UBICACION
            'M' => 32,  // OBSERVACIONES
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}
