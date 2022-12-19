<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; 
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServicesExcel implements FromCollection, ShouldAutoSize, WithHeadings,  WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $services;

    public function __construct($services) {
            $this->services = $services;
    }

    public function collection()
    {
        return collect($this->services);
    }

    public function headings(): array
    {
        return [
            'Host',
            'Service',
            'State',
            'Start Time',
            'End Time',
            'Description',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
