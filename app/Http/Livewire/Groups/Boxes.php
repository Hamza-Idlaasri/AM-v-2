<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Boxes extends Component
{
    public function render()
    {
        $members = $this->getMembers();

        $member_equips = $this->getMemberEquips();

        $boxgroups = collect($this->getBoxGroups($members,$member_equips));

        return view('livewire.groups.boxes')
            ->with(['boxgroups' => $boxgroups])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getMembers()
    {
        return DB::table('nagios_hostgroups')
            ->join('nagios_hostgroup_members','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostgroup_members.host_object_id')
            ->join('nagios_hoststatus','nagios_hoststatus.host_object_id','=','nagios_hosts.host_object_id')
            ->select('nagios_hosts.alias as type','nagios_hostgroups.alias as boxgroup_name','nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_hoststatus.current_state','nagios_hostgroups.hostgroup_id')
            ->where('nagios_hosts.alias','box')
            ->get();
    }

    public function getMemberEquips()
    {
        return DB::table('nagios_hosts')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->select('nagios_services.display_name as equip_name','nagios_servicestatus.current_state','nagios_hosts.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->get();
    }

    public function getBoxGroups($members,$member_equips)
    {

        $members_details = [];

        foreach ($members as $member) {
            
            $equips_ok = 0;
            $equips_warning = 0;
            $equips_critical = 0;
            $equips_unknown = 0;

            foreach ($member_equips as $equip) {

                if($equip->host_object_id == $member->host_object_id)
                {
                    switch ($equip->current_state) {

                        case 0:
                            $equips_ok++;
                            break;

                        case 1:
                            $equips_warning++;
                            break;

                        case 2:
                            $equips_critical++;
                            break;

                        case 3:
                            $equips_unknown++;
                            break;
                    }

                } else {
                    continue;
                }

            }

            $member_details = (object)[
                'hostgroup_id' => $member->hostgroup_id,
                'host_object_id' => $member->host_object_id,
                'box_name' => $member->box_name,
                'box_state' => $member->current_state,
                'equips_ok' => $equips_ok,
                'equips_warning' => $equips_warning,
                'equips_critical' => $equips_critical,
                'equips_unknown' => $equips_unknown
            ];


            array_push($members_details, $member_details);

        }

        $nagios_hostgroups = DB::table('nagios_hostgroups')
            ->select('nagios_hostgroups.hostgroup_id','nagios_hostgroups.alias as boxgroup_name')
            ->get();

        $boxgroups = [];

        foreach ($nagios_hostgroups as $boxgroup) {

            $all_members = [];
            
            foreach ($members_details as $details) {
                
                if($details->hostgroup_id == $boxgroup->hostgroup_id)
                {
                    array_push($all_members, $details);
                    
                } else
                    continue;

            }

            if($all_members)
                array_push($boxgroups, (object)['hostgroup_id' => $boxgroup->hostgroup_id,'boxgroup_name' => $boxgroup->boxgroup_name,'members' => (object)$all_members]);

        }

        return $boxgroups;
    }
}
