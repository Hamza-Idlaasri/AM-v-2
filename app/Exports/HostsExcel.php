<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HostsExcel implements FromCollection, WithHeadings
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


}
