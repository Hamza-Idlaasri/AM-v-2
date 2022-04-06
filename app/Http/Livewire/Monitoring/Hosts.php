<?php

namespace App\Http\Livewire\Monitoring;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Hosts extends Component
{
    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $hosts = $this->getHosts()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(10);

        } else {

            $hosts = $this->getHosts()->paginate(10);

        }

        return view('livewire.monitoring.hosts')
            ->with(['hosts' => $hosts])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getHosts()
    {
        return DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->orderBy('display_name');
    }
}
