<?php

namespace App\Http\Livewire\Config\Add\Services;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Service extends Component
{
    public function render()
    {
        $hosts = DB::table('nagios_hosts')
            ->where('alias','host')
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.*')
            ->get();

        $windows = ['CPU Usage','RAM','Process Count','Disk C'];

        $linux = ['Current Load','Total Processes','Current Users','SSH','HTTP','Root Partition','Swap Usage'];

        $printer = ['PING (printer)','Printer Status'];

        return view('livewire.config.add.services.service')
        ->with(['hosts' => $hosts, 'windows' => $windows, 'linux' => $linux, 'printer' => $printer])
        ->extends('layouts.app')
        ->section('content');
    }
}
