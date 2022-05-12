<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class NetworkMap extends Component
{
    public function render()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $hosts = DB::table('nagios_hosts')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->get();

        $parent_hosts = DB::table('nagios_hosts')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->join('nagios_host_parenthosts','nagios_hosts.host_id','=','nagios_host_parenthosts.host_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->get();

        return view('livewire.network-map')
            ->with('hosts', $hosts)
            ->with('parent_hosts', $parent_hosts)
            ->extends('layouts.app')
            ->section('content');
    }
}
