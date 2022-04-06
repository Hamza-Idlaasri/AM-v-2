<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ServicesExcel implements FromCollection, WithHeadings
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
}
