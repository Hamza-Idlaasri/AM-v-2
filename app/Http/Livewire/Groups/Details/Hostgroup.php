<?php

namespace App\Http\Livewire\Groups\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Hostgroup extends Component
{
    public $members;
    public $hostgroup;

    public function mount(Request $request)
    {
        $this->members = DB::table('nagios_hostgroups')
            ->join('nagios_hostgroup_members','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostgroup_members.host_object_id')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.alias as type','nagios_hostgroups.alias as hostgroup','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_hosts.host_id','nagios_hostgroups.hostgroup_id','nagios_services.display_name as service_name','nagios_servicestatus.current_state','nagios_servicestatus.output','nagios_servicestatus.last_check','nagios_hosts.host_object_id')
            ->where('nagios_hostgroups.hostgroup_id','=',$request->id)
            ->orderBy('nagios_hosts.display_name')
            ->get();

        $this->hostgroup = DB::table('nagios_hostgroups')
            ->where('hostgroup_id','=',$request->id)
            ->get();
    }

    public function render()
    {
        return view('livewire.groups.details.hostgroup')
            ->with(['hostgroup' => $this->hostgroup,'members' => $this->members])
            ->extends('layouts.app')
            ->section('content');
    }
}
