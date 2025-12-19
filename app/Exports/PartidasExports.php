<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Partida;
use DB;

class PartidasExports implements FromView, ShouldAutoSize, WithEvents
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
            'partidas' => $this->getCollection()
        ]);
    }

    public function getCollection()
    {
        $termino = $this->termino;

        // Initialize Query FIRST
        $query = Partida::with('container')
            ->select('tipo', 'marca', 'modelo', 'año', 'codInv', 'expediente', 'status', DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") as fecha_creacion'));

        // Filter Logic
        // Status Filter
        if ($this->status === 'DISPONIBLE') {
            // Strictly check for 'status' column AND no bill presence for extra safety
            $query->where('status', '!=', 'VENDIDO')
                ->whereDoesntHave('bill');
        } elseif ($this->status === 'VENDIDO') {
            $query->where(function ($q) {
                // Either explicitly marked status OR has a bill
                $q->where('status', 'VENDIDO')->orHas('bill');
            });
        }

        // Date Logic (Created At vs Sold Date)
        if ($this->startDate && $this->endDate) {
            if ($this->status === 'VENDIDO') {
                // If filtering by SOLD, check the Bill Date
                $query->whereHas('bill', function ($q) {
                    $q->whereBetween('fecha', [$this->startDate, $this->endDate]);
                });
            } else {
                // Default: Filter by Registration Date
                $query->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            }
        }

        // Type filtering
        if (isset($this->caso) && in_array($this->caso, ['AUTOPARTE', 'CÁMARA'])) {
            $query->where('tipo', $this->caso);
        } else {
            $query->whereNotIn('tipo', ['AUTOPARTE', 'CÁMARA']);
        }

        $query->where(function ($query) use ($termino) {
            $query->where('tipo', 'like', "%{$termino}%")
                ->orWhere('marca', 'like', "%{$termino}%")
                ->orWhere('modelo', 'like', "%{$termino}%")
                ->orWhere('año', 'like', "%{$termino}%")
                ->orWhere('codInv', 'like', "%{$termino}%")
                ->orWhere('expediente', 'like', "%{$termino}%")
                ->orWhere('status', 'like', "%{$termino}%");
        });

        return $query->get();
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
                $sheet->getPageSetup()->setHorizontalCentered(true);
            },
        ];
    }

    public function store($export, $fileName)
    {
        // Exporta el documento
        $export->store('exports/' . $fileName, 'local');
    }
}