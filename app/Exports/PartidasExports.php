<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Inventario;
use DB;

class PartidasExports implements FromView, WithEvents, ShouldAutoSize, WithColumnWidths
{
    protected $termino;
    protected $caso;
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($caso, $termino, $startDate = null, $endDate = null, $status = null)
    {
        $this->termino = $termino;
        $this->caso = $caso;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function view(): View
    {
        return view('exports.partidas', [
            'partidas' => $this->getCollection(),
            'isExcel' => true
        ]);
    }

    public function getCollection()
    {
        $termino = $this->termino;

        // Initialize Query FIRST
        $query = Inventario::with('container', 'bill')
            ->select(
                'inventarios.id',
                'tipo',
                'marca',
                'modelo',
                'serial',
                'año',
                'codInv',
                'expediente',
                'status',
                'observation',
                // DB::raw('DATE_FORMAT(billings.fecha, "%d/%m/%Y") as fecha_venta'),
                // 'billings.numero_factura',
                DB::raw('DATE_FORMAT(inventarios.created_at, "%d/%m/%Y") as fecha_creacion')
            );
        // ->leftJoin('billings', 'inventarios.id', '=', 'billings.partida_id');

        // Filter Logic
        // Status Filter
        if ($this->status === 'DISPONIBLE') {
            $query->where('status', '!=', 'VENDIDO')
                ->whereDoesntHave('bill');
        } elseif ($this->status === 'VENDIDO') {
            $query->where(function ($q) {
                $q->where('status', 'VENDIDO')->orHas('bill');
            });
        }

        // Date Logic (Created At vs Sold Date)
        if ($this->startDate && $this->endDate) {
            if ($this->status === 'VENDIDO') {
                $query->whereHas('bill', function ($q) {
                    $q->whereBetween('fecha', [$this->startDate, $this->endDate]);
                });
            } else {
                $query->whereBetween('inventarios.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            }
        }

        // Type filtering
        if (isset($this->caso) && in_array($this->caso, ['AUTOPARTE', 'CÁMARA'])) {
            $query->where('tipo', $this->caso);
        } else {
            $query->whereNotIn('tipo', ['AUTOPARTE', 'CÁMARA']);
        }

        // Search filtering
        if ($termino && !in_array(strtolower($termino), ['todos', 'null', 'all'])) {
            $query->where(function ($query) use ($termino) {
                $query->where('tipo', 'like', "%{$termino}%")
                    ->orWhere('marca', 'like', "%{$termino}%")
                    ->orWhere('modelo', 'like', "%{$termino}%")
                    ->orWhere('serial', 'like', "%{$termino}%")
                    ->orWhere('año', 'like', "%{$termino}%")
                    ->orWhere('codInv', 'like', "%{$termino}%")
                    ->orWhere('expediente', 'like', "%{$termino}%")
                    ->orWhere('status', 'like', "%{$termino}%");
            });
        }

        return $query->get();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // TIPO
            'B' => 15, // MARCA
            'C' => 30, // MODELO
            'D' => 20, // SERIAL
            'E' => 10, // AÑO
            'F' => 15, // COD INV
            'G' => 15, // EXPEDIENTE
            'H' => 15, // ESTATUS
            'I' => 15, // F VENTA
            'J' => 20, // NRO FACTURA
            'K' => 30, // OBSERVACIÓN
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Page Setup for PDF
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                // $sheet->getPageSetup()->setHorizontalCentered(true);
            },
        ];
    }

    public function store($export, $fileName)
    {
        // Exporta el documento
        $export->store('exports/' . $fileName, 'local');
    }
}