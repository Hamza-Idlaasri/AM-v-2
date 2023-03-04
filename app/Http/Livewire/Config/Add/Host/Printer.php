<?php

namespace App\Http\Livewire\Config\Add\Host;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use App\Models\Sites;

class Printer extends Component
{
    public function render()
    {
        $sites = Sites::all()->except(1);

        $hosts = $this->getHosts();

        return view('livewire.config.add.host.printer')
        ->with(['hosts' => $hosts, 'sites' => $sites])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getHosts()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == "All") {

            return DB::table('nagios_hosts')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->where('nagios_hosts.alias','host')
                ->get();

        } else {
            
            return DB::table('nagios_hosts')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->where('nagios_hosts.alias','host')
                ->get();
        }
    }
}
