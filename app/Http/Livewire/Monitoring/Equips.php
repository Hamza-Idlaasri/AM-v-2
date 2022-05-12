<?php

namespace App\Http\Livewire\Monitoring;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\UsersSite;

class Equips extends Component
{
    use WithPagination;

    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $equips =$this->getEquips()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(10);

        } else {

            $equips = $this->getEquips()->paginate(10);

        }

        return view('livewire.monitoring.equips')
        ->with(['equips'=>$equips,'search' => $this->search])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getEquips()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
        ->where('alias','box')
        ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
        ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
        ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
        ->where('nagios_customvariables.varvalue',$site_name)
        ->select('nagios_hosts.display_name as box_name','nagios_hosts.*','nagios_services.display_name as equip_name','nagios_services.*','nagios_servicestatus.*')
        ->orderBy('nagios_hosts.display_name');
    }
}
