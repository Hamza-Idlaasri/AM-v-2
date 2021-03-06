<?php

namespace App\Http\Livewire\Config\Display;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

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
                ->get();

        } else {

            $hosts = $this->getHosts()->get();

        }

        return view('livewire.config.display.hosts')
            ->with('hosts', $hosts)
            ->extends('layouts.app')
            ->section('content');
    }

    public function getHosts()
    { 
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->where('nagios_customvariables.varvalue',$site_name);
    }
    
}
