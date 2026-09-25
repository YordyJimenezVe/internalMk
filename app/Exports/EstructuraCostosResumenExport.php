<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class EstructuraCostosResumenExport implements FromView, WithEvents, ShouldAutoSize, WithColumnWidths, WithTitle
{
    protected $termino;
    protected $caso;
    protected $startDate;
    protected $endDate;
    protected $status;

    public function title(): string
    {
        return 'Resumen por Tipo y Costo';
    }

    public function __construct($caso = null, $termino = null, $startDate = null, $endDate = null, $status = null)
    {
        $this->caso = $caso;
        $this->termino = $termino;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function view(): View
    {
        $detalleExport = new EstructuraCostosDetalleExport($this->caso, $this->termino, $this->startDate, $this->endDate, $this->status);
        $items = $detalleExport->getCollection();

        // Group items by (tipo + costo_usd)
        $groupsMap = [];
        $resumenPorTipoMap = [];

        foreach ($items as $item) {
            $tipo = !empty($item->tipo) ? trim($item->tipo) : 'SIN ESPECIFICAR';
            $costoUsd = (float) ($item->costo_usd_calc ?? 0);
            $baseUsd = (float) ($item->base_imponible_usd_calc ?? 0);

            $key = $tipo . '|||' . number_format($costoUsd, 2, '.', '');

            if (!isset($groupsMap[$key])) {
                $groupsMap[$key] = [
                    'tipo' => $tipo,
                    'costo_usd' => $costoUsd,
                    'base_imponible_usd' => $baseUsd,
                    'cantidad' => 0,
                    'total_costo_usd' => 0,
                    'total_base_imponible_usd' => 0,
                ];
            }

            $groupsMap[$key]['cantidad'] += 1;
            $groupsMap[$key]['total_costo_usd'] += $costoUsd;
            $groupsMap[$key]['total_base_imponible_usd'] += $baseUsd;

            // Summary strictly by tipo
            if (!isset($resumenPorTipoMap[$tipo])) {
                $resumenPorTipoMap[$tipo] = [
                    'cantidad' => 0,
                    'total_costo_usd' => 0,
                    'total_base_imponible_usd' => 0,
                ];
            }
            $resumenPorTipoMap[$tipo]['cantidad'] += 1;
            $resumenPorTipoMap[$tipo]['total_costo_usd'] += $costoUsd;
            $resumenPorTipoMap[$tipo]['total_base_imponible_usd'] += $baseUsd;
        }

        // Sort groups by tipo then by costo_usd
        usort($groupsMap, function ($a, $b) {
            if ($a['tipo'] === $b['tipo']) {
                return $b['costo_usd'] <=> $a['costo_usd'];
            }
            return strcmp($a['tipo'], $b['tipo']);
        });

        ksort($resumenPorTipoMap);

        return view('exports.estructura_costos_resumen', [
            'grupos' => $groupsMap,
            'resumenPorTipo' => $resumenPorTipoMap,
            'isExcel' => true
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // N°
            'B' => 32, // TIPO DE REGISTRO / PRODUCTO
            'C' => 22, // COSTO UNITARIO BASE ($)
            'D' => 24, // BASE IMPONIBLE UNITARIA ($)
            'E' => 18, // CANTIDAD (PIEZAS)
            'F' => 24, // TOTAL COSTO BASE ($)
            'G' => 26, // TOTAL BASE IMPONIBLE ($)
            'H' => 18, // % DEL INVENTARIO
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

                $maxRow = $sheet->getHighestRow();

                // Enable AutoFilter on row 5 (Main Grouped Table)
                $sheet->setAutoFilter('A5:H5');

                // Currency formatting for USD
                $usdFormat = '"$"#,##0.00';
                $sheet->getStyle('C6:C' . $maxRow)->getNumberFormat()->setFormatCode($usdFormat);
                $sheet->getStyle('D6:D' . $maxRow)->getNumberFormat()->setFormatCode($usdFormat);
                $sheet->getStyle('F6:F' . $maxRow)->getNumberFormat()->setFormatCode($usdFormat);
                $sheet->getStyle('G6:G' . $maxRow)->getNumberFormat()->setFormatCode($usdFormat);

                // Quantity integer format
                $intFormat = '#,##0';
                $sheet->getStyle('E6:E' . $maxRow)->getNumberFormat()->setFormatCode($intFormat);
            },
        ];
    }
}
