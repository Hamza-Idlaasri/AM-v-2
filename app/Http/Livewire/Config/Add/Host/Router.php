<?php

namespace App\Http\Livewire\Config\Add\Host;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Router extends Component
{
    public function render()
    {
        $hosts = $this->getHosts();

        return view('livewire.config.add.host.router')
        ->with(['hosts' => $hosts])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getHosts()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->where('nagios_hosts.alias','host')
            ->get();
    }
}
