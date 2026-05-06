<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Inventario;
use DB;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\AfterSheet;

class HistoryExports implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $termino; // Store the filtered termino value
    protected $caso;

    public function __construct($caso, $termino)
    {
        $this->termino = $termino;
        $this->caso = $caso;
    }

    public function collection()
    {
        $termino = $this->termino;
        // Define la colección de datos para exportar
        $response = Inventario::with('container')
            ->select('tipo', 'marca', 'modelo', 'año', 'codInv', 'expediente', 'status', DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") as fecha_creacion'))
            ->where(function ($query) use ($termino) {
                // Filtrar por múltiples campos
                $query->where('tipo', 'like', "%{$termino}%")
                    ->orWhere('marca', 'like', "%{$termino}%")
                    ->orWhere('modelo', 'like', "%{$termino}%")
                    ->orWhere('año', 'like', "%{$termino}%")
                    ->orWhere('codInv', 'like', "%{$termino}%")
                    ->orWhere('expediente', 'like', "%{$termino}%")
                    ->orWhere('status', 'like', "%{$termino}%");
            })
            ->get();
        return $response;
    }
    public function headings(): array
    {
        // Define los encabezados de las columnas
        return [
            'tipo',
            'marca',
            'modelo',
            'año',
            'codInv',
            'expediente',
            'status',
            'fecha de registro',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Define los estilos generales del libro
        return [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
    }
    public function registerEvents(): array
    {
        // Supuestamente define horientación de texto pero de momento no funciona
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:A1000')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
            },
        ];
    }
    public function store($export, $fileName)
    {
        // Exporta el documento
        $export->store('exports/' . $fileName, 'local');
    }

}