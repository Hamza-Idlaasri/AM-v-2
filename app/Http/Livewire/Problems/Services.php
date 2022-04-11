<?php

namespace App\Http\Livewire\Problems;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class Services extends Component
{
    use WithPagination;

    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $services =$this->getServices()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(10);

        } else {

            $services = $this->getServices()->paginate(10);

        }

        return view('livewire.problems.services')
        ->with(['services' => $services,'search' => $this->search])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getServices()
    {
        return DB::table('nagios_hosts')
        ->where('alias','host')
        ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
        ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
        ->select('nagios_hosts.display_name as host_name','nagios_hosts.*','nagios_services.display_name as service_name','nagios_services.*','nagios_servicestatus.*')
        ->where('current_state','<>','0')
        ->orderBy('nagios_hosts.display_name');
    }
}
