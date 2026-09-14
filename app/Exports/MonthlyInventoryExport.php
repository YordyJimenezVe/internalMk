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
        $month = $this->data['monthName'] ?? 'Mensual';
        $year = $this->data['year'] ?? '';
        $rawTitle = "Inv {$month} {$year}";
        
        $cleanTitle = preg_replace('/[\\\\\/*?:\[\]]/', '', $rawTitle);
        return mb_substr($cleanTitle, 0, 30);
    }

    public function view(): View
    {
        return view('reports.monthly_inventory', array_merge($this->data, ['isExcel' => true]));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Marca / Modelo
            'B' => 32, // Producto / Descripción
            'C' => 25, // Lotes (Contenedores)
            'D' => 12, // Unid Inicial
            'E' => 12, // Unid Entradas
            'F' => 12, // Unid Salidas
            'G' => 12, // Unid Retiros
            'H' => 14, // Unid Autoconsumo
            'I' => 14, // Unid Final
            'J' => 18, // Val Inicial
            'K' => 16, // Val Entradas
            'L' => 16, // Val Salidas
            'M' => 16, // Val Retiros
            'N' => 18, // Val Autoconsumo
            'O' => 18, // Val Final
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
