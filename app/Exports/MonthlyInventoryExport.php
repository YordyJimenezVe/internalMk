<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonthlyInventoryExport implements FromView, WithEvents, WithColumnWidths, WithTitle, WithPreCalculateFormulas
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
            'A' => 18, // CÓDIGO
            'B' => 36, // DESCRIPCIÓN
            'C' => 11, // Unid Inicial
            'D' => 12, // Unid Entradas
            'E' => 11, // Unid Salidas
            'F' => 11, // Unid Retiros
            'G' => 13, // Unid Autoconsumo
            'H' => 12, // Unid Final
            'I' => 16, // Val Inicial
            'J' => 16, // Val Entradas
            'K' => 16, // Val Salidas
            'L' => 16, // Val Retiros
            'M' => 16, // Val Autoconsumo
            'N' => 18, // Val Final
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
                $highestColumn = 'N';

                // Fusionar celdas del encabezado oficial centrado (Filas 1 a 4)
                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->mergeCells("A3:{$highestColumn}3");
                $sheet->mergeCells("A4:{$highestColumn}4");

                // Estilos de los títulos principales del reporte
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true)->setColor(new Color('0F172A'));
                $sheet->getStyle('A2')->getFont()->setSize(10)->setBold(true)->setColor(new Color('334155'));
                $sheet->getStyle('A3')->getFont()->setSize(10)->setBold(true)->setColor(new Color('334155'));
                $sheet->getStyle('A4')->getFont()->setSize(11)->setBold(true)->setColor(new Color('475569'));

                $sheet->getStyle("A1:{$highestColumn}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Alturas de filas iniciales
                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(22);
                $sheet->getRowDimension(5)->setRowHeight(10); // Fila separadora vacía
                $sheet->getRowDimension(6)->setRowHeight(24); // Cabecera tabla
                $sheet->getRowDimension(7)->setRowHeight(20); // Subcabecera tabla

                // Estilos para la cabecera principal de la tabla (Fila 6)
                // A6:B7 para CÓDIGO y DESCRIPCIÓN
                $sheet->getStyle('A6:B7')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('1E293B');
                $sheet->getStyle('A6:B7')->getFont()->setColor(new Color('FFFFFF'))->setBold(true);
                $sheet->getStyle('A6:B7')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // C6:H6 (Unidades Físicas) - Azul
                $sheet->getStyle('C6:H6')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('0284C7');
                $sheet->getStyle('C6:H6')->getFont()->setColor(new Color('FFFFFF'))->setBold(true)->setSize(11);
                $sheet->getStyle('C6:H6')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // I6:N6 (Valores Bolívares) - Verde
                $sheet->getStyle('I6:N6')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('15803D');
                $sheet->getStyle('I6:N6')->getFont()->setColor(new Color('FFFFFF'))->setBold(true)->setSize(11);
                $sheet->getStyle('I6:N6')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Estilos para subcabeceras (Fila 7)
                $sheet->getStyle('C7:H7')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('E0F2FE');
                $sheet->getStyle('C7:H7')->getFont()->setColor(new Color('0369A1'))->setBold(true);
                $sheet->getStyle('C7:H7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('I7:N7')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('DCFCE7');
                $sheet->getStyle('I7:N7')->getFont()->setColor(new Color('15803D'))->setBold(true);
                $sheet->getStyle('I7:N7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Ajuste de alineación vertical y wrap text para datos
                if ($highestRow >= 8) {
                    $sheet->getStyle("A8:{$highestColumn}{$highestRow}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP)
                        ->setWrapText(true);

                    // Formato numérico en Excel con supresión de ceros (#,##0;-#,##0;;@ y #,##0.00;-#,##0.00;;@)
                    $sheet->getStyle("C8:H{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0;-#,##0;;@');

                    $sheet->getStyle("I8:N{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00;-#,##0.00;;@');

                    // Recorrer filas para estilizar Secciones de Marca, Subtotales, Totales y Firmas
                    for ($r = 8; $r <= $highestRow; $r++) {
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
                            $sheet->mergeCells("A{$r}:B{$r}");
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('E2E8F0');
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFont()
                                ->setColor(new Color('0F172A'))
                                ->setBold(true);
                            $sheet->getStyle("A{$r}:B{$r}")->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            $sheet->getRowDimension($r)->setRowHeight(20);
                        } elseif (str_starts_with(trim($cellVal), 'TOTALES GENERALES')) {
                            $sheet->mergeCells("A{$r}:B{$r}");
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('0F172A');
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFont()
                                ->setColor(new Color('FFFFFF'))
                                ->setBold(true)
                                ->setSize(10);
                            $sheet->getStyle("A{$r}:B{$r}")->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            $sheet->getRowDimension($r)->setRowHeight(22);

                            // Bordes delgados para la cuadrícula completa de la tabla (desde cabecera A6 hasta fila de Totales Generales)
                            $sheet->getStyle("A6:{$highestColumn}{$r}")
                                ->getBorders()
                                ->getAllBorders()
                                ->setBorderStyle(Border::BORDER_THIN)
                                ->setColor(new Color('CBD5E1'));
                        } elseif (str_starts_with(trim($cellVal), 'FIRMA')) {
                            $sheet->mergeCells("A{$r}:F{$r}");
                            $sheet->mergeCells("I{$r}:N{$r}");
                            $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFont()->setBold(true)->setSize(10);
                            $sheet->getStyle("A{$r}:F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("I{$r}:N{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            $sheet->getRowDimension($r)->setRowHeight(28);
                        } else {
                            if ($r % 2 === 0) {
                                $sheet->getStyle("A{$r}:{$highestColumn}{$r}")->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB('F8FAFC');
                            }
                        }
                    }
                }
            },
        ];
    }
}
