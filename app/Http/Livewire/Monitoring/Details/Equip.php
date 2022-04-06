<?php

namespace App\Http\Livewire\Monitoring\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Equip extends Component
{
    public $equip;

    public function mount(Request $request)
    {
        $this->equip = DB::table('nagios_hosts')
            ->where('alias','box')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.display_name as box_name','nagios_hosts.*','nagios_services.display_name as equip_name','nagios_services.*','nagios_servicestatus.*')
            ->where('nagios_services.service_object_id',$request->id)
            ->get();
    }

    public function render()
    {
        return view('livewire.monitoring.details.equip')
            ->with(['equip' => $this->equip])
            ->extends('layouts.app')
            ->section('content');
    }
}
