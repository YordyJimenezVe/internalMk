<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Maintenance;
use DB;

class MaintenanceExports implements FromView, ShouldAutoSize, WithEvents
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
        return view('exports.maintenances', [
            'maintenances' => $this->getCollection()
        ]);
    }

    public function getCollection()
    {
        $termino = $this->termino;

        $query = Maintenance::with('partida')
            ->select('id', 'tipo', 'status', 'nombre_mecanico', 'apellido_mecanico', 'partida_id', 'fecha');

        // Filter by Termino (Search) - Only if not 'todos' or 'null'
        if ($termino && !in_array(strtolower($termino), ['todos', 'null', 'all'])) {
            $query->where(function ($q) use ($termino) {
                $q->where('nombre_mecanico', 'like', "%{$termino}%")
                    ->orWhere('apellido_mecanico', 'like', "%{$termino}%")
                    ->orWhere('tipo', 'like', "%{$termino}%")
                    ->orWhereHas('partida', function ($q2) use ($termino) {
                        $q2->where('marca', 'like', "%{$termino}%")
                            ->orWhere('modelo', 'like', "%{$termino}%")
                            ->orWhere('codInv', 'like', "%{$termino}%")
                            ->orWhere('expediente', 'like', "%{$termino}%");
                    });
            });
        }

        // Filter by Status
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Date Logic
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('fecha', [$this->startDate, $this->endDate]);
        }

        return $query->orderBy('fecha', 'desc')->get();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageSetup()->setHorizontalCentered(true);
            },
        ];
    }
}
