<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Equips extends Component
{
    public function render()
    {
        $equipgroups = $this->getMembers();

        return view('livewire.groups.equips')
            ->with(['equipgroups' => $equipgroups])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getEquipGroups()
    {
        return DB::table('nagios_servicegroups')
            ->join('nagios_servicegroup_members','nagios_servicegroups.servicegroup_id','=','nagios_servicegroup_members.servicegroup_id')
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->select('nagios_servicegroups.alias as equipgroup_name','nagios_servicegroups.servicegroup_object_id','nagios_servicegroups.servicegroup_id','nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name','nagios_servicestatus.current_state as equip_state','nagios_hoststatus.current_state as box_state')
            ->where('nagios_hosts.alias','box')
            ->get();
    }

    public function getMembers()
    {
        $equipgroups = $this->getEquipGroups();

        $groups = [];

        foreach ($equipgroups as $equipgroup) {
            
            $members = DB::table('nagios_servicegroups')
                ->join('nagios_servicegroup_members','nagios_servicegroups.servicegroup_id','=','nagios_servicegroup_members.servicegroup_id')
                ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->select('nagios_servicegroups.alias as equipgroup_name','nagios_servicegroups.servicegroup_object_id','nagios_servicegroups.servicegroup_id','nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name','nagios_servicestatus.current_state as equip_state','nagios_hoststatus.current_state as box_state')
                ->where('nagios_servicegroups.servicegroup_object_id', $equipgroup->servicegroup_object_id)
                ->get();

            $equipgroup_members = [];

            foreach ($members as $member) {
                array_push($equipgroup_members,['box_name' => $member->box_name,'box_state' => $member->box_state,'equip_name' => $member->equip_name,'equip_state' => $member->equip_state]);
            }

            array_push($groups,(object)['equipgroup_name' => $equipgroup->equipgroup_name,'servicegroup_id' => $equipgroup->servicegroup_id,'members' => $equipgroup_members]);

        }

        return  array_values(array_unique($groups, SORT_REGULAR));
    }
}
