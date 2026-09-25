<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EstructuraCostosExport implements WithMultipleSheets
{
    protected $termino;
    protected $caso;
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($caso = null, $termino = null, $startDate = null, $endDate = null, $status = null)
    {
        $this->caso = $caso;
        $this->termino = $termino;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    /**
     * Devuelve las dos hojas del libro de Excel:
     * 1. Estructura de Costos Detallada (EstructuraCostosDetalleExport)
     * 2. Resumen Agrupado por Tipo y Costo (EstructuraCostosResumenExport)
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            new EstructuraCostosDetalleExport($this->caso, $this->termino, $this->startDate, $this->endDate, $this->status),
            new EstructuraCostosResumenExport($this->caso, $this->termino, $this->startDate, $this->endDate, $this->status),
        ];
    }

    /**
     * Mantiene compatibilidad con la generación de PDFs y vistas individuales.
     */
    public function getCollection()
    {
        return (new EstructuraCostosDetalleExport($this->caso, $this->termino, $this->startDate, $this->endDate, $this->status))->getCollection();
    }
}
