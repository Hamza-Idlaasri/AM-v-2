<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; 
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class HostsExcel implements FromCollection, ShouldAutoSize, WithHeadings,  WithStyles, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $hosts;

    public function __construct($hosts) {
            $this->hosts = $hosts;
    }

    public function collection()
    {
        return collect($this->hosts);
    }

    public function headings(): array
    {
        return [
            'Host',
            'Address IP',
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

    public function map($hosts): array
    {
        return [
            $hosts->host_name,
            $hosts->address,
            $hosts->state,
            $hosts->start_time,
            $hosts->end_time,
            $hosts->output
        ];
    }
}
