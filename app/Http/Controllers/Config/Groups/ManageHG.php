<?php

namespace App\Http\Controllers\Config\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageHG extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function manageHG($hostgroup_id)
    {
        $hostgroup = DB::table('nagios_hostgroups')
        ->where('nagios_hostgroups.hostgroup_id', $hostgroup_id)
        ->join('nagios_hostgroup_members','nagios_hostgroups.hostgroup_id','=','nagios_hostgroup_members.hostgroup_id')
        ->select('nagios_hostgroups.alias as hostgroup_name','nagios_hostgroups.hostgroup_id')
        ->first();
        
        $members = DB::table('nagios_hostgroups')
        ->where('nagios_hostgroups.hostgroup_id', $hostgroup_id)
        ->join('nagios_hostgroup_members','nagios_hostgroups.hostgroup_id','=','nagios_hostgroup_members.hostgroup_id')
        ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
        ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
        ->get();

        $hosts = DB::table('nagios_hosts')
        ->where('alias','host')
        ->select('nagios_hosts.host_object_id','nagios_hosts.display_name as host_name')
        ->get();
        
        $all_members = [];

        foreach ($members as $member) {
            array_push($all_members, $member->host_object_id);    
        }

        return view('config.groups.editHG', compact('hostgroup', 'all_members', 'hosts'));
    }
}
