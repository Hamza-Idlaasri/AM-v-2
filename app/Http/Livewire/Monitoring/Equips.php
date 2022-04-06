<?php

namespace App\Http\Livewire\Monitoring;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Equips extends Component
{
    protected $equips;

    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $this->equips =$this->getEquips()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(10);

        } else {

            $this->equips = $this->getEquips()->paginate(10);

        }

        return view('livewire.monitoring.equips')
        ->with(['equips'=>$this->equips])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getEquips()
    {
        return DB::table('nagios_hosts')
        ->where('alias','box')
        ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
        ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
        ->select('nagios_hosts.display_name as box_name','nagios_hosts.*','nagios_services.display_name as equip_name','nagios_services.*','nagios_servicestatus.*')
        ->orderBy('nagios_hosts.display_name');
    }
}
