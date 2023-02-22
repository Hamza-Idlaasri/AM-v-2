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
            'Site',
            'Ville',
            'Equipement',
            // 'Pin',
            'State',
            'State Time',
            'Durée',
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
            $equips->site_name,
            $equips->equip_name,
            // substr($equip->check_command,9,-2),
            $this->convertState($equips->state),
            $equips->state_time,
            $this->formatSeconds($equips->state_time_usec),
            // $equips->end_time,
            $this->output($equips->state, $equips->pin_name)
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

    public function output($state,$pin_name)
    {
        if ($state == 0) {
            return 'fonction normalement';
        } else {
            return $pin_name;
        }
    }

    function formatSeconds($seconds) {

        $weeks = floor($seconds / 604800);
        $seconds -= $weeks * 604800;
        $days = floor($seconds / 86400);
        $seconds -= $days * 86400;
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        $output = '';
        if ($weeks > 0) {
            $output .= $weeks . ' w, ';
        }
        if ($days > 0) {
            $output .= $days . ' d, ';
        }
        if ($hours > 0) {
            $output .= $hours . ' h, ';
        }
        if ($minutes > 0) {
            $output .= $minutes . ' m, ';
        }
        $output .= $seconds . ' s';
        return $output;
    }
}
