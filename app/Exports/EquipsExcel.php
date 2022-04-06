<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EquipsExcel implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $equips;

    public function __construct($equips) {
            $this->equips = $equips;
    }

    public function collection()
    {
        return collect($this->equips);
    }

    public function headings(): array
    {
        return [
            'Box',
            'Equipement',
            'State',
            'Start Time',
            'End Time',
            'Description',
        ];
    }
}
