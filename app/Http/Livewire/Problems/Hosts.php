<?php

namespace App\Http\Livewire\Problems;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Hosts extends Component
{
    protected $hosts;

    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $this->hosts =$this->getHosts()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(10);

        } else {

            $this->hosts = $this->getHosts()->paginate(10);

        }

        return view('livewire.problems.hosts')
        ->with(['hosts' => $this->hosts])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getHosts()
    {
        return DB::table('nagios_hosts')
        ->where('alias','host')
        ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
        ->where('current_state','<>','0')
        ->orderBy('display_name');
    }
}
