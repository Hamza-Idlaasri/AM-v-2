<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Services extends Component
{
    public function render()
    {
        $servicegroups = $this->getMembers();

        return view('livewire.groups.services')
            ->with(['servicegroups' => $servicegroups])
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
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->select('nagios_servicegroups.alias as servicegroup_name','nagios_servicegroups.servicegroup_object_id','nagios_servicegroups.servicegroup_id','nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_servicestatus.current_state as service_state','nagios_hoststatus.current_state as host_state')
            ->where('nagios_hosts.alias','host')
            ->get();

    }

    public function getMembers()
    {
        $servicegroups = $this->getServiceGroups();

        $groups = [];

        foreach ($servicegroups as $servicegroup) {
            
            $members = DB::table('nagios_servicegroups')
                ->join('nagios_servicegroup_members','nagios_servicegroups.servicegroup_id','=','nagios_servicegroup_members.servicegroup_id')
                ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->select('nagios_servicegroups.alias as servicegroup_name','nagios_servicegroups.servicegroup_object_id','nagios_servicegroups.servicegroup_id','nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_servicestatus.current_state as service_state','nagios_hoststatus.current_state as host_state')
                ->where('nagios_servicegroups.servicegroup_object_id', $servicegroup->servicegroup_object_id)
                ->get();

            $servicegroup_members = [];

            foreach ($members as $member) {
                array_push($servicegroup_members,['host_name' => $member->host_name,'host_state' => $member->host_state,'service_name' => $member->service_name,'service_state' => $member->service_state]);
            }

            array_push($groups,(object)['servicegroup_name' => $servicegroup->servicegroup_name,'servicegroup_id' => $servicegroup->servicegroup_id,'members' => $servicegroup_members]);

        }

        return  array_values(array_unique($groups, SORT_REGULAR));
    }
}
