<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class MonthlyInventoryExport implements FromView, WithEvents, ShouldAutoSize, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Inventario ' . ($this->data['monthName'] ?? 'Mensual');
    }

    public function view(): View
    {
        return view('reports.monthly_inventory', array_merge($this->data, ['isExcel' => true]));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, // Código
            'B' => 35, // Producto
            'C' => 14, // Unid Inicial
            'D' => 12, // Unid Entradas
            'E' => 12, // Unid Salidas
            'F' => 12, // Unid Retiros
            'G' => 14, // Unid Autoconsumo
            'H' => 14, // Unid Final
            'I' => 18, // Val Inicial
            'J' => 16, // Val Entradas
            'K' => 16, // Val Salidas
            'L' => 16, // Val Retiros
            'M' => 18, // Val Autoconsumo
            'N' => 18, // Val Final
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LETTER);
            },
        ];
    }
}
