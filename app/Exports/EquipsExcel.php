<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; 
use Maatwebsite\Excel\Concerns\ShouldAutoSize; 
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class EquipsExcel implements FromCollection, ShouldAutoSize, WithHeadings,  WithStyles, WithMapping
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

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function map($equips): array
    {
        return [
            $equips->box_name,
            $equips->equip_name,
            $this->convertState($equips->state),
            $equips->start_time,
            $equips->end_time,
            $equips->output
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
