<?php

namespace App\Http\Livewire\Groups\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Servicegroup extends Component
{
    public $sg_id;

    public function mount(Request $request)
    {
        $this->sg_id = $request->id;
    }

    public function render()
    {
        $servicegroup = DB::table('nagios_servicegroups')
            ->where('servicegroup_id',$this->sg_id)
            ->get();

        $members = DB::table('nagios_servicegroups')
            ->join('nagios_servicegroup_members','nagios_servicegroups.servicegroup_id','=','nagios_servicegroup_members.servicegroup_id')
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->select('nagios_hosts.alias as type','nagios_hosts.display_name as host_name','nagios_hoststatus.current_state as host_state','nagios_services.display_name as service_name','nagios_servicestatus.current_state as service_state','nagios_servicestatus.last_check','nagios_servicestatus.output','nagios_servicegroups.alias as servicegroup_name','nagios_servicegroups.servicegroup_id')
            ->where('nagios_servicegroups.servicegroup_id',$this->sg_id)
            ->orderBy('nagios_hosts.display_name')
            ->get();

        return view('livewire.groups.details.servicegroup')
            ->with(['servicegroup' => $servicegroup,'members' => $members])
            ->extends('layouts.app')
            ->section('content');
    }
}
