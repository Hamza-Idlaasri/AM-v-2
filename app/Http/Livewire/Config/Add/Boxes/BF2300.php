<?php

namespace App\Http\Livewire\Config\Add\Boxes;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use App\Models\Sites;

class BF2300 extends Component
{
    public $site_name;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        $hosts = $this->getHosts();

        $sites = Sites::all()->except(1);

        return view('livewire.config.add.boxes.bf2300')
            ->with(['hosts' => $hosts, 'sites' => $sites, 'site_name' => $this->site_name])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getHosts()
    {
        return DB::table('nagios_hosts')
            ->join('nagios_hoststatus', 'nagios_hosts.host_object_id', '=', 'nagios_hoststatus.host_object_id')
            ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
            ->where('nagios_hosts.alias', 'host')
            ->where('nagios_customvariables.varvalue', $this->site_name)
            ->get();
    }
}
