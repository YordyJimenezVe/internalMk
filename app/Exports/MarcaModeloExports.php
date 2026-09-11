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
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Clase de exportación a Excel para el Reporte de Inventario Agrupado por Marca y Modelo.
 *
 * Consolida la cantidad de repuestos/motores por cada combinación de Marca y Modelo,
 * calculando stock disponible, otros estatus, subtotales y el desglose de contenedores de origen.
 */
class MarcaModeloExports implements FromView, WithEvents, ShouldAutoSize, WithColumnWidths, WithTitle
{
    protected $termino;
    protected $caso;
    protected $startDate;
    protected $endDate;
    protected $status;

    /**
     * Título de la pestaña de la hoja de cálculo (máximo 31 caracteres).
     *
     * @return string
     */
    public function title(): string
    {
        return 'Marca y Modelo';
    }

    /**
     * Constructor de la exportación por Marca y Modelo.
     *
     * @param  string|null  $caso  Filtro por tipo de categoría o caso especial.
     * @param  string|null  $termino  Término de búsqueda rápida.
     * @param  string|null  $startDate  Fecha inicio para rango de filtros.
     * @param  string|null  $endDate  Fecha fin para rango de filtros.
     * @param  string|null  $status  Estado del inventario (DISPONIBLE, VENDIDO, ALL, etc.).
     */
    public function __construct($caso = null, $termino = null, $startDate = null, $endDate = null, $status = null)
    {
        $this->caso = $caso;
        $this->termino = $termino;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    /**
     * Retorna la vista Blade que genera la estructura del Excel.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        $data = $this->getGroupedData();

        return view('exports.marca_modelo', [
            'groups' => $data['groups'],
            'kpis' => $data['kpis'],
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'statusFilter' => $this->status,
            'casoFilter' => $this->caso,
            'isExcel' => true
        ]);
    }

    /**
     * Procesa la consulta de base de datos y realiza la agrupación por Marca y Modelo.
     *
     * @return array
     */
    public function getGroupedData()
    {
        $query = Inventario::with('container', 'bill');

        // Filtro por Estatus
        if ($this->status === 'DISPONIBLE') {
            $query->where('status', '!=', 'VENDIDO')
                ->whereDoesntHave('bill');
        } elseif ($this->status === 'VENDIDO') {
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
        }

        // Filtro por Rango de Fechas
        if ($this->startDate && $this->endDate) {
            if ($this->status === 'VENDIDO') {
                $query->whereHas('bill', function ($q) {
                    $q->whereBetween('fecha', [$this->startDate, $this->endDate]);
                });
            } else {
                $query->whereBetween('inventarios.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            }
        }

        // Filtro por Categoría / Tipo (Caso)
        if (isset($this->caso) && in_array($this->caso, ['AUTOPARTE', 'CÁMARA', 'MOTOR', 'CAJA'])) {
            $query->where('tipo', 'LIKE', "%{$this->caso}%");
        }

        // Búsqueda general
        $termino = $this->termino;
        if ($termino && !in_array(strtolower($termino), ['todos', 'null', 'all'])) {
            $query->where(function ($q) use ($termino) {
                $q->where('tipo', 'like', "%{$termino}%")
                    ->orWhere('marca', 'like', "%{$termino}%")
                    ->orWhere('modelo', 'like', "%{$termino}%")
                    ->orWhere('serial', 'like', "%{$termino}%")
                    ->orWhere('codInv', 'like', "%{$termino}%");
            });
        }

        $items = $query->get();

        // Agrupamiento por Marca y luego por Modelo
        $grouped = [];
        $totalItems = $items->count();
        $totalDisponibles = 0;
        $totalOtros = 0;
        $marcasSet = [];
        $modelosSet = [];
        $contenedoresSet = [];

        foreach ($items as $item) {
            $marca = !empty($item->marca) ? mb_strtoupper(trim($item->marca)) : 'SIN MARCA';
            $modelo = !empty($item->modelo) ? mb_strtoupper(trim($item->modelo)) : 'SIN MODELO';
            $tipo = !empty($item->tipo) ? mb_strtoupper(trim($item->tipo)) : 'GENERAL';
            $isDisponible = ($item->status !== 'VENDIDO' && $item->bill->isEmpty());

            $marcasSet[$marca] = true;
            $modelosSet[$marca . '-' . $modelo] = true;

            $containerCode = 'S/C';
            if ($item->container && !empty($item->container->cod)) {
                $containerCode = $item->container->cod;
            } elseif (!empty($item->expediente)) {
                $containerCode = 'EXP-' . $item->expediente;
            }
            $contenedoresSet[$containerCode] = true;

            if ($isDisponible) {
                $totalDisponibles++;
            } else {
                $totalOtros++;
            }

            if (!isset($grouped[$marca])) {
                $grouped[$marca] = [
                    'marca' => $marca,
                    'total_unidades' => 0,
                    'total_disponibles' => 0,
                    'total_otros' => 0,
                    'modelos' => []
                ];
            }

            if (!isset($grouped[$marca]['modelos'][$modelo])) {
                $grouped[$marca]['modelos'][$modelo] = [
                    'modelo' => $modelo,
                    'tipo' => $tipo,
                    'disponibles' => 0,
                    'otros' => 0,
                    'total' => 0,
                    'contenedores' => []
                ];
            }

            $grouped[$marca]['total_unidades']++;
            if ($isDisponible) {
                $grouped[$marca]['total_disponibles']++;
                $grouped[$marca]['modelos'][$modelo]['disponibles']++;
            } else {
                $grouped[$marca]['total_otros']++;
                $grouped[$marca]['modelos'][$modelo]['otros']++;
            }

            $grouped[$marca]['modelos'][$modelo]['total']++;

            if (!isset($grouped[$marca]['modelos'][$modelo]['contenedores'][$containerCode])) {
                $grouped[$marca]['modelos'][$modelo]['contenedores'][$containerCode] = 0;
            }
            $grouped[$marca]['modelos'][$modelo]['contenedores'][$containerCode]++;
        }

        // Formatear la cadena de contenedores por cada modelo
        foreach ($grouped as &$marcaData) {
            foreach ($marcaData['modelos'] as &$modeloData) {
                $contParts = [];
                foreach ($modeloData['contenedores'] as $cCode => $count) {
                    $contParts[] = "{$cCode} ({$count} u.)";
                }
                $modeloData['contenedores_str'] = implode(', ', $contParts);
            }
        }

        $kpis = [
            'total_marcas' => count($marcasSet),
            'total_modelos' => count($modelosSet),
            'total_piezas' => $totalItems,
            'total_disponibles' => $totalDisponibles,
            'total_otros' => $totalOtros,
            'total_contenedores' => count($contenedoresSet),
        ];

        return [
            'groups' => $grouped,
            'kpis' => $kpis
        ];
    }

    /**
     * Ancho de columnas predefinido para una presentación óptima.
     *
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 22, // MARCA
            'B' => 30, // MODELO / ESPECIFICACIÓN
            'C' => 18, // TIPO
            'D' => 16, // STOCK DISPONIBLE
            'E' => 16, // OTROS ESTATUS
            'F' => 16, // TOTAL PIEZAS
            'G' => 50, // CONTENEDORES DE ORIGEN
        ];
    }

    /**
     * Registrar eventos de configuración de hoja (landscape y ajuste para PDF/Excel).
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}
