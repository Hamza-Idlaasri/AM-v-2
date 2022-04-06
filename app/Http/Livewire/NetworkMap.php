<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class NetworkMap extends Component
{
    public function render()
    {
        $hosts = DB::table('nagios_hosts')
        ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
        ->get();

        $parent_hosts = DB::table('nagios_hosts')
        ->join('nagios_host_parenthosts','nagios_hosts.host_id','=','nagios_host_parenthosts.host_id')
        ->get();

        return view('livewire.network-map')
            ->with('hosts', $hosts)->with('parent_hosts', $parent_hosts)
            ->extends('layouts.app')
            ->section('content');
    }
}
