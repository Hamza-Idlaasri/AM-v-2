<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Services extends Component
{
    public function render()
    {
        $servicegroups = $this->getServiceGroups();

        $members = $this->getMembers();

        return view('livewire.groups.services')
            ->with(['servicegroups' => $servicegroups,'members' => $members])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getServiceGroups()
    {
        return DB::table('nagios_servicegroups')
            ->join('nagios_servicegroup_members','nagios_servicegroups.servicegroup_id','=','nagios_servicegroup_members.servicegroup_id')
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->select('nagios_servicegroups.alias as servicegroup','nagios_servicegroups.servicegroup_id')
            ->where('nagios_hosts.alias','host')
            ->take(1)
            ->get();

    }

    public function getMembers()
    {
        return DB::table('nagios_servicegroups')
            ->join('nagios_servicegroup_members','nagios_servicegroups.servicegroup_id','=','nagios_servicegroup_members.servicegroup_id')
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->select('nagios_hosts.alias as type','nagios_hosts.display_name as host_name','nagios_hoststatus.current_state as host_state','nagios_services.display_name as service_name','nagios_servicestatus.current_state as service_state','nagios_servicegroups.alias as servicegroup_name','nagios_servicegroups.servicegroup_id')
            ->where('nagios_hosts.alias','host')
            ->get();
    }
}
