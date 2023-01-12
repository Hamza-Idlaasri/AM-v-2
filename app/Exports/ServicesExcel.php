<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; 
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class ServicesExcel implements FromCollection, ShouldAutoSize, WithHeadings,  WithStyles, WithMapping
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

    public function map($services): array
    {
        return [
            $services->host_name,
            $services->service_name,
            $this->convertState($services->state),
            $services->start_time,
            $services->end_time,
            $services->output
        ];
    }

    public function convertState($state)
    {
        switch ($state) {
            case 0:
                return  'Ok';
                break;
            case 1:
                return  'Warning';
                break;
            case 2:
                return  'Critical';
                break;
            case 3:
                return  'Unknown';
                break;
        }
    }
}
