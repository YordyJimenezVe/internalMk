<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Inventario;
use App\Models\ExchangeRate;

class EstructuraCostosDetalleExport implements FromView, WithEvents, ShouldAutoSize, WithColumnWidths, WithTitle
{
    protected $termino;
    protected $caso;
    protected $startDate;
    protected $endDate;
    protected $status;

    public function title(): string
    {
        return 'Estructura de Costos';
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
        return view('exports.estructura_costos', [
            'partidas' => $this->getCollection(),
            'isExcel' => true
        ]);
    }

    public function getCollection()
    {
        $latestRate = ExchangeRate::where('source', 'BCV')->latest()->first();
        $officialTasa = $latestRate ? (float) $latestRate->rate : 0;

        $query = Inventario::with('container', 'bill', 'maintenances');

        // Status Filtering: Por defecto solo se incluye inventario disponible (excluye VENDIDO)
        if ($this->status === 'VENDIDO') {
            $query->where(function ($q) {
                $q->where('status', 'VENDIDO')->orHas('bill');
            });
        } elseif ($this->status === 'GARANTIA') {
            $query->whereIn('status', ['GARANTIA', 'GARANTÍA']);
        } elseif ($this->status === 'PRECIO PENDIENTE') {
            $query->where('status', 'PRECIO PENDIENTE');
        } elseif ($this->status === 'INOPERATIVO-DESARMADO') {
            $query->where('status', 'INOPERATIVO-DESARMADO');
        } elseif ($this->status === 'USO INTERNO') {
            $query->where('status', 'USO INTERNO');
        } else {
            // Por defecto (DISPONIBLE, ALL o sin especificar): Solo lo DISPONIBLE (NO VENDIDO)
            $query->where('status', '!=', 'VENDIDO')
                ->whereDoesntHave('bill');
        }

        // Date Filtering
        if ($this->startDate && $this->endDate) {
            if ($this->status === 'VENDIDO') {
                $query->whereHas('bill', function ($q) {
                    $q->whereBetween('fecha', [$this->startDate, $this->endDate]);
                });
            } else {
                $query->whereBetween('inventarios.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            }
        }

        // Category/Caso Filtering
        if (isset($this->caso) && in_array($this->caso, ['AUTOPARTE', 'CÁMARA', 'MOTOR', 'CAJA'])) {
            $query->where('tipo', 'LIKE', "%{$this->caso}%");
        }

        // Search Term Filtering
        $termino = $this->termino;
        if ($termino && !in_array(strtolower($termino), ['todos', 'null', 'all', 'general'])) {
            $query->where(function ($q) use ($termino) {
                $q->where('tipo', 'like', "%{$termino}%")
                    ->orWhere('marca', 'like', "%{$termino}%")
                    ->orWhere('modelo', 'like', "%{$termino}%")
                    ->orWhere('serial', 'like', "%{$termino}%")
                    ->orWhere('codInv', 'like', "%{$termino}%")
                    ->orWhere('expediente', 'like', "%{$termino}%");
            });
        }

        $items = $query->orderBy('inventarios.id', 'desc')->get();

        // Calculate exact cost structure fields for each item
        return $items->map(function ($item) use ($officialTasa) {
            $itemTasa = ((float)($item->tasa_bcv ?? 0) > 0) ? (float)$item->tasa_bcv : null;
            $containerTasa = ($item->container && (float)$item->container->tasa_bcv > 0) ? (float)$item->container->tasa_bcv : null;
            $tasa = $itemTasa ?? ($containerTasa ?? $officialTasa);

            $costoBs = (float) ($item->costo_importacion_unitario ?? 0);
            $costoUsd = (float) ($item->costo ?? ($tasa > 0 && $costoBs > 0 ? round($costoBs / $tasa, 2) : 0));

            $prorrateoBs = (float) ($item->prorrateo_gastos ?? ($item->container->prorrateo_gastos ?? 0));
            $costoTallerBs = (float) $item->costo_taller;
            $costoLandedBs = $costoBs + $prorrateoBs + $costoTallerBs;

            $utilidadPercent = (float) ($item->porcentaje_utilidad ?? ($item->container->porcentaje_utilidad ?? 20));
            
            $baseImponibleBs = (float) ($item->price > 0 ? $item->price : round($costoLandedBs * (1 + ($utilidadPercent / 100)), 2));
            $baseImponibleUsd = $tasa > 0 ? round($baseImponibleBs / $tasa, 2) : 0;

            $ivaBs = round($baseImponibleBs * 0.16, 2);
            $ivaUsd = $tasa > 0 ? round($ivaBs / $tasa, 2) : 0;

            $precioConIvaBs = round($baseImponibleBs * 1.16, 2);
            $precioVentaComercialUsd = (float) ($item->price_sale > 0 ? $item->price_sale : ($tasa > 0 ? round($precioConIvaBs / $tasa, 2) : 0));

            $item->tasa_bcv_aplicada = $tasa;
            $item->costo_usd_calc = $costoUsd;
            $item->costo_bs_calc = $costoBs;
            $item->prorrateo_bs_calc = $prorrateoBs;
            $item->costo_taller_bs_calc = $costoTallerBs;
            $item->costo_landed_bs_calc = $costoLandedBs;
            $item->utilidad_percent_calc = $utilidadPercent;
            $item->base_imponible_bs_calc = $baseImponibleBs;
            $item->base_imponible_usd_calc = $baseImponibleUsd;
            $item->iva_bs_calc = $ivaBs;
            $item->iva_usd_calc = $ivaUsd;
            $item->precio_con_iva_bs_calc = $precioConIvaBs;
            $item->precio_venta_usd_calc = $precioVentaComercialUsd;

            return $item;
        });
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, // COD. INV
            'B' => 16, // TIPO
            'C' => 14, // MARCA
            'D' => 20, // MODELO
            'E' => 18, // SERIAL
            'F' => 8,  // AÑO
            'G' => 14, // EXPEDIENTE
            'H' => 14, // CONTENEDOR
            'I' => 14, // FECHA TASA
            'J' => 14, // TASA BCV
            'K' => 16, // COSTO IMP USD
            'L' => 18, // COSTO IMP BS
            'M' => 16, // PRORRATEO BS
            'N' => 16, // COSTO TALLER BS
            'O' => 18, // COSTO LANDED BS
            'P' => 12, // % UTILIDAD
            'Q' => 18, // B.I.G. BS
            'R' => 16, // B.I.G. USD
            'S' => 16, // 16% IVA BS
            'T' => 16, // 16% IVA USD
            'U' => 20, // PRECIO CON IVA BS
            'V' => 22, // PRECIO VENTA USD
            'W' => 14, // ESTATUS
            'X' => 25, // OBSERVACIONES
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

                // Enable AutoFilter on row 5
                $sheet->setAutoFilter('A5:X5');

                // Column J: TASA BCV (4 decimal places)
                $sheet->getStyle('J6:J' . $maxRow)->getNumberFormat()->setFormatCode('#,##0.0000');

                // USD Currency Columns: K, R, T, V
                $usdFormat = '"$"#,##0.00';
                $sheet->getStyle('K6:K' . $maxRow)->getNumberFormat()->setFormatCode($usdFormat);
                $sheet->getStyle('R6:R' . $maxRow)->getNumberFormat()->setFormatCode($usdFormat);
                $sheet->getStyle('T6:T' . $maxRow)->getNumberFormat()->setFormatCode($usdFormat);
                $sheet->getStyle('V6:V' . $maxRow)->getNumberFormat()->setFormatCode($usdFormat);

                // Bs Currency Columns: L, M, N, O, Q, S, U
                $bsFormat = '"Bs. "#,##0.00';
                $sheet->getStyle('L6:L' . $maxRow)->getNumberFormat()->setFormatCode($bsFormat);
                $sheet->getStyle('M6:M' . $maxRow)->getNumberFormat()->setFormatCode($bsFormat);
                $sheet->getStyle('N6:N' . $maxRow)->getNumberFormat()->setFormatCode($bsFormat);
                $sheet->getStyle('O6:O' . $maxRow)->getNumberFormat()->setFormatCode($bsFormat);
                $sheet->getStyle('Q6:Q' . $maxRow)->getNumberFormat()->setFormatCode($bsFormat);
                $sheet->getStyle('S6:S' . $maxRow)->getNumberFormat()->setFormatCode($bsFormat);
                $sheet->getStyle('U6:U' . $maxRow)->getNumberFormat()->setFormatCode($bsFormat);

                // Percentage Column: P (% Utilidad)
                $sheet->getStyle('P6:P' . $maxRow)->getNumberFormat()->setFormatCode('#,##0.00"%"');

                // Summary Row totals formatting
                $summaryValueRow = $maxRow;
                $sheet->getStyle('E' . $summaryValueRow)->getNumberFormat()->setFormatCode($bsFormat);
                $sheet->getStyle('J' . $summaryValueRow)->getNumberFormat()->setFormatCode($bsFormat);
                $sheet->getStyle('O' . $summaryValueRow)->getNumberFormat()->setFormatCode($bsFormat);
                $sheet->getStyle('T' . $summaryValueRow)->getNumberFormat()->setFormatCode($usdFormat);
            },
        ];
    }
}
