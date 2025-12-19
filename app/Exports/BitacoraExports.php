<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\Bitacora;
use DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BitacoraExports implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
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
        $response = Bitacora::select('users.name', 'action', 'description', DB::raw('DATE_FORMAT(bitacoras.created_at, "%Y-%m-%d-%H:%i") as created_at'))
            ->join('users', 'bitacoras.users_id', '=', 'users.id')
            ->where(function ($query) use ($termino) {
                $query->where('bitacoras.id', 'like', "%{$termino}%")
                    ->orWhere('bitacoras.action', 'like', "%{$termino}%")
                    ->orWhere('bitacoras.description', 'like', "%{$termino}%")
                    ->orWhere('bitacoras.created_at', 'like', "%{$termino}%");
            })
            ->get();
        return $response;
    }
    public function headings(): array
    {
        // Define los encabezados de las columnas
        return [
            'Usuario',
            'Acción',
            'Descripción',
            'Fecha',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_TIME4,
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('Y/m/d'),
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet
                    ->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->getDelegate()->getStyle('A:D')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A:D')->getFont()->setName('Arial');
                $event->sheet->getDelegate()->getStyle('A:D')->getFont()->setSize(10);

            },
        ];
    }
    public function store($export, $fileName)
    {
        $export->store('exports/' . $fileName, 'local');
    }

}