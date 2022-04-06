<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BoxesExcel implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $boxes;

    public function __construct($boxes) {
            $this->boxes = $boxes;
    }

    public function collection()
    {
        return collect($this->boxes);
    }

    public function headings(): array
    {
        return [
            'Box',
            'Address IP',
            'State',
            'Start Time',
            'End Time',
            'Description',
        ];
    }
}
