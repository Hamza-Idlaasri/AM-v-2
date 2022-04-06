<?php

namespace App\Http\Livewire\Config\Add\Host;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Switchs extends Component
{
    public function render()
    {
        $hosts = $this->getHosts();

        return view('livewire.config.add.host.switchs')
        ->with(['hosts' => $hosts])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getHosts()
    {
        return DB::table('nagios_hosts')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->get();
    }
}
