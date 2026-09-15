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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MonthlyInventoryExport implements FromView, WithEvents, WithColumnWidths, WithTitle
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
        return ($this->data['periodType'] ?? 'monthly') === 'bimonthly' ? 'Inventario Bimensual' : 'Inventario Mensual';
    }

    public function view(): View
    {
        return view('reports.monthly_inventory', array_merge($this->data, ['isExcel' => true]));
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, // Marca
            'B' => 18, // Tipo de Producto
            'C' => 20, // Modelo
            'D' => 11, // Unid Inicial
            'E' => 12, // Unid Entradas
            'F' => 11, // Unid Salidas
            'G' => 11, // Unid Retiros
            'H' => 13, // Unid Autoconsumo
            'I' => 11, // Unid Final
            'J' => 15, // Val Inicial
            'K' => 15, // Val Entradas
            'L' => 15, // Val Salidas
            'M' => 15, // Val Retiros
            'N' => 16, // Val Autoconsumo
            'O' => 18, // Val Final
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Configuración de página horizontal y pie de página impreso (Firma y Sello en cada página)
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LETTER);
                $sheet->getHeaderFooter()->setOddFooter('&L&"Arial,Bold" FIRMA &R&"Arial,Bold" SELLO');
                $sheet->getHeaderFooter()->setEvenFooter('&L&"Arial,Bold" FIRMA &R&"Arial,Bold" SELLO');

                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'O';

                // Fusionar celdas del encabezado superior de la empresa (Filas 1 a 3)
                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('J1:O1');
                $sheet->mergeCells('A2:I2');
                $sheet->mergeCells('J2:O2');
                $sheet->mergeCells('A3:I3');
                $sheet->mergeCells('J3:O3');

                // Estilos de los títulos principales del reporte
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true)->setColor(new Color('0F172A'));
                $sheet->getStyle('A2')->getFont()->setSize(11)->setBold(true)->setColor(new Color('475569'));

                // Alineación a la derecha para la caja RIF / Fecha / Tasa
                $sheet->getStyle('J1:O3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('J1:O3')->getFont()->setBold(true);

                // Alturas de filas iniciales
                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(10); // Fila separadora vacía
                $sheet->getRowDimension(5)->setRowHeight(24); // Cabecera tabla
                $sheet->getRowDimension(6)->setRowHeight(20); // Subcabecera tabla

                // Estilos para la cabecera principal de la tabla (Fila 5)
                // A5:C6 para Marca, Modelo, Producto
                $sheet->getStyle('A5:C6')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('1E293B');
                $sheet->getStyle('A5:C6')->getFont()->setColor(new Color('FFFFFF'))->setBold(true);
                $sheet->getStyle('A5:C6')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // D5:I5 (Unidades Físicas) - Azul
                $sheet->getStyle('D5:I5')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('0284C7');
                $sheet->getStyle('D5:I5')->getFont()->setColor(new Color('FFFFFF'))->setBold(true)->setSize(11);
                $sheet->getStyle('D5:I5')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // J5:O5 (Valores Bolívares) - Verde
                $sheet->getStyle('J5:O5')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('15803D');
                $sheet->getStyle('J5:O5')->getFont()->setColor(new Color('FFFFFF'))->setBold(true)->setSize(11);
                $sheet->getStyle('J5:O5')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Estilos para subcabeceras (Fila 6)
                $sheet->getStyle('D6:I6')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('E0F2FE');
                $sheet->getStyle('D6:I6')->getFont()->setColor(new Color('0369A1'))->setBold(true);
                $sheet->getStyle('D6:I6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('J6:O6')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('DCFCE7');
                $sheet->getStyle('J6:O6')->getFont()->setColor(new Color('15803D'))->setBold(true);
                $sheet->getStyle('J6:O6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Ajuste de alineación vertical y wrap text para datos
                if ($highestRow >= 7) {
                    $sheet->getStyle("A7:{$highestColumn}{$highestRow}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP)
                        ->setWrapText(true);

                    // Formato numérico general en Excel
                    $sheet->getStyle("D7:I{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');

                    $sheet->getStyle("J7:O{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');

                    // Recorrer filas para estilizar Secciones de Marca y Subtotales
                    for ($r = 7; $r < $highestRow; $r++) {
                        $cellVal = (string) $sheet->getCell("A{$r}")->getValue();

                        if (str_starts_with(trim($cellVal), 'MARCA:')) {
                            $sheet->mergeCells("A{$r}:{$highestColumn}{$r}");
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('1E293B');
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFont()
                                ->setColor(new Color('FFFFFF'))
                                ->setBold(true)
                                ->setSize(11);
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                                ->setVertical(Alignment::VERTICAL_CENTER);
                            $sheet->getRowDimension($r)->setRowHeight(24);
                        } elseif (str_starts_with(trim($cellVal), 'SUBTOTAL')) {
                            $sheet->mergeCells("A{$r}:C{$r}");
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('E2E8F0');
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFont()
                                ->setColor(new Color('0F172A'))
                                ->setBold(true);
                            $sheet->getStyle("A{$r}:C{$r}")->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            $sheet->getRowDimension($r)->setRowHeight(20);
                        } else {
                            if ($r % 2 === 0) {
                                $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB('F8FAFC');
                            }
                        }
                    }

                    // Bordes delgados para la cuadrícula
                    $sheet->getStyle("A5:{$highestColumn}{$highestRow}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->setColor(new Color('CBD5E1'));

                    // Estilo de la Fila de Totales Generales (Última fila)
                    $sheet->getStyle("A{$highestRow}:{$highestColumn}{$highestRow}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('0F172A');

                    $sheet->getStyle("A{$highestRow}:{$highestColumn}{$highestRow}")
                        ->getFont()
                        ->setColor(new Color('FFFFFF'))
                        ->setBold(true)
                        ->setSize(10);

                    $sheet->mergeCells("A{$highestRow}:C{$highestRow}");
                    $sheet->getStyle("A{$highestRow}:C{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
