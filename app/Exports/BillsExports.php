<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Billing;
use DB;

class BillsExports implements FromView, ShouldAutoSize, WithEvents
{
    protected $termino;
    protected $caso;
    protected $startDate;
    protected $endDate;

    public function __construct($caso, $termino, $startDate = null, $endDate = null)
    {
        $this->caso = $caso;
        $this->termino = $termino;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('exports.bills', [
            'bills' => $this->getCollection()
        ]);
    }

    public function getCollection()
    {
        $termino = $this->termino;

        $query = Billing::select('partida_id', 'partidas.tipo', 'partidas.marca', 'partidas.modelo', 'billings.fecha', 'numero_factura', 'numero_control', 'billings.divisa')
            ->join('partidas', 'billings.partida_id', '=', 'partidas.id');

        // Date Filter
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('billings.fecha', [$this->startDate, $this->endDate]);
        }

        $query->where(function ($query) use ($termino) {
            $query->where('partidas.id', 'like', "%{$termino}%")
                ->orWhere('partidas.marca', 'like', "%{$termino}%")
                ->orWhere('partidas.modelo', 'like', "%{$termino}%");
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