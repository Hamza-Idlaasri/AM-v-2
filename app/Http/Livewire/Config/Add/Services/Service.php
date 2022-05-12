<?php

namespace App\Http\Livewire\Config\Add\Services;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Service extends Component
{
    public function render()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $hosts = DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
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
