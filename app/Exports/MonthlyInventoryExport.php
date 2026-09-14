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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class MonthlyInventoryExport implements FromView, WithEvents, ShouldAutoSize, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Título de la pestaña de la hoja de cálculo en Excel (Máximo 31 caracteres).
     */
    public function title(): string
    {
        return 'Inventario Mensual';
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

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Habilitar ajuste automático de texto (saltos de línea en contenedores) y alineación superior
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_TOP)
                    ->setWrapText(true);

                // Bordes para la tabla de inventario
                if ($highestRow >= 9) {
                    $sheet->getStyle("A9:{$highestColumn}{$highestRow}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->setColor(new Color('CBD5E1'));
                }
            },
        ];
    }
}
