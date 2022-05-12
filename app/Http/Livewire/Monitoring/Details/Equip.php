<?php

namespace App\Http\Livewire\Monitoring\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Equip extends Component
{
    public $equip_id;

    public function mount(Request $request)
    {
        $this->equip_id = $request->id;
    }

    public function render()
    {
        $equip = DB::table('nagios_hosts')
            ->where('alias','box')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.display_name as box_name','nagios_hosts.*','nagios_services.display_name as equip_name','nagios_services.*','nagios_servicestatus.*')
            ->where('nagios_services.service_object_id',$this->equip_id)
            ->first();

        return view('livewire.monitoring.details.equip')
            ->with(['equip' => $equip])
            ->extends('layouts.app')
            ->section('content');
    }
}
