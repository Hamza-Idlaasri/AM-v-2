<?php

namespace App\Http\Controllers\Config\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageEG extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function manageEG($equipgroup_id)
    {
        $equipgroup = DB::table('nagios_servicegroups')
            ->where('servicegroup_id',$equipgroup_id)
            ->select('nagios_servicegroups.alias as equipgroup_name','nagios_servicegroups.servicegroup_id')
            ->first();

        $members = DB::table('nagios_servicegroups')
            ->where('nagios_servicegroups.servicegroup_id',$equipgroup_id)
            ->join('nagios_servicegroup_members','nagios_servicegroups.servicegroup_id','=','nagios_servicegroup_members.servicegroup_id')
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->select('nagios_hosts.alias as type','nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_servicegroups.alias as servicegroup_name','nagios_servicegroups.servicegroup_id','nagios_services.service_object_id')
            ->get();
        
        $equips = DB::table('nagios_services')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_hosts.display_name as box_name')
            ->get();

        $all_members = [];

        foreach ($members as $member) {
            array_push($all_members, $member->service_object_id);
        }

        return view('config.groups.editEG', compact('equipgroup','all_members','equips'));  
    }
}
