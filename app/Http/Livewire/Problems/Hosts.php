<?php

namespace App\Http\Livewire\Problems;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\UsersSite;

class Hosts extends Component
{
    use WithPagination;

    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $hosts =$this->getHosts()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(10);

        } else {

            $hosts = $this->getHosts()->paginate(10);

        }

        return view('livewire.problems.hosts')
        ->with(['hosts' => $hosts,'search' => $this->search])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getHosts()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->where('current_state','<>','0')
            ->orderBy('display_name');
    }
}
