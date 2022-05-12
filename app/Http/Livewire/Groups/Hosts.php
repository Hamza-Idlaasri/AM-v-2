<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Hosts extends Component
{
    public function render()
    {
        $members = $this->getMembers();

        $member_services = $this->getMemberServices();

        $hostgroups = collect($this->getHostGroups($members,$member_services));

        return view('livewire.groups.hosts')
            ->with(['hostgroups' => $hostgroups])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getMembers()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hostgroups')
            ->join('nagios_hostgroup_members','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostgroup_members.host_object_id')
            ->join('nagios_hoststatus','nagios_hoststatus.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.alias as type','nagios_hostgroups.alias as hostgroup_name','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_hoststatus.current_state','nagios_hostgroups.hostgroup_id')
            ->where('nagios_hosts.alias','host')
            ->get();
    }

    public function getMemberServices()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_services.display_name as service_name','nagios_servicestatus.current_state','nagios_hosts.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->get();
    }

    public function getHostGroups($members,$member_services)
    {

        $members_details = [];

        foreach ($members as $member) {
            
            $services_ok = 0;
            $services_warning = 0;
            $services_critical = 0;
            $services_unknown = 0;

            foreach ($member_services as $service) {

                if($service->host_object_id == $member->host_object_id)
                {
                    switch ($service->current_state) {

                        case 0:
                            $services_ok++;
                            break;

                        case 1:
                            $services_warning++;
                            break;

                        case 2:
                            $services_critical++;
                            break;

                        case 3:
                            $services_unknown++;
                            break;
                    }

                } else {
                    continue;
                }

            }

            $member_details = (object)[
                'hostgroup_id' => $member->hostgroup_id,
                // 'hostgroup_name' => $member->hostgroup_name,
                'host_object_id' => $member->host_object_id,
                'host_name' => $member->host_name,
                'host_state' => $member->current_state,
                'services_ok' => $services_ok,
                'services_warning' => $services_warning,
                'services_critical' => $services_critical,
                'services_unknown' => $services_unknown
            ];


            array_push($members_details, $member_details);

        }

        $nagios_hostgroups = DB::table('nagios_hostgroups')
            ->select('nagios_hostgroups.hostgroup_id','nagios_hostgroups.alias as hostgroup_name')
            ->get();

        $hostgroups = [];

        foreach ($nagios_hostgroups as $hostgroup) {

            $all_members = [];
            
            foreach ($members_details as $details) {
                
                if($details->hostgroup_id == $hostgroup->hostgroup_id)
                {
                    array_push($all_members, $details);
                    
                } else
                    continue;

            }

            if($all_members)
                array_push($hostgroups, (object)['hostgroup_id' => $hostgroup->hostgroup_id,'hostgroup_name' => $hostgroup->hostgroup_name,'members' => (object)$all_members]);

        }

        return $hostgroups;
    }

}
