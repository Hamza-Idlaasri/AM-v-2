<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; 
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class BoxesExcel implements FromCollection, ShouldAutoSize, WithHeadings,  WithStyles, WithMapping
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
            'State Time',
            // 'End Time',
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

    public function map($boxes): array
    {
        return [
            $boxes->box_name,
            $boxes->address,
            $this->convertState($boxes->state),
            $boxes->state_time,
            // $boxes->end_time,
            $this->description($boxes->state)
        ];
    }

    public function convertState($state)
    {
        switch ($state) {
            case 0:
                return  'Up';
                break;
            case 1:
                return  'Down';
                break;
            case 2:
                return  'Unreachable';
                break;
        }
    }

    public function description($state)
    {
        switch ($state) {
            case 0:
                return 'fonction normalement';
                break;
            case 1:
                return 'le box est OFF';
                break;
            case 2:
                return 'difficulté à reconnaître l\'état du box, vérifier si le box est ON';
                break;
        }
    }
}
