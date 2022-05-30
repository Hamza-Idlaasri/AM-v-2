<?php

namespace App\Http\Livewire\Groups\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Boxgroup extends Component
{
    public $bg_id;

    public function mount(Request $request)
    {
        $this->bg_id = $request->id;        
    }

    public function render()
    {
        $members = DB::table('nagios_hostgroups')
            ->join('nagios_hostgroup_members','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostgroup_members.host_object_id')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.alias as type','nagios_hostgroups.alias as boxgroup','nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_hosts.host_id','nagios_hostgroups.hostgroup_id','nagios_services.display_name as equip_name','nagios_servicestatus.current_state','nagios_servicestatus.output','nagios_servicestatus.last_check','nagios_hosts.host_object_id')
            ->where('nagios_hostgroups.hostgroup_id','=',$this->bg_id)
            ->orderBy('nagios_hosts.display_name')
            ->get();

        $boxgroup = DB::table('nagios_hostgroups')
            ->where('hostgroup_id','=',$this->bg_id)
            ->get();

        return view('livewire.groups.details.boxgroup')
            ->with(['boxgroup' => $boxgroup,'members' => $members])
            ->extends('layouts.app')
            ->section('content');
    }
}
