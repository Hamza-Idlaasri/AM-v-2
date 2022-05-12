<?php

namespace App\Http\Controllers\Config\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageBG extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function manageBG($boxgroup_id)
    {
        $boxgroup = DB::table('nagios_hostgroups')
        ->where('nagios_hostgroups.hostgroup_id', $boxgroup_id)
        ->join('nagios_hostgroup_members','nagios_hostgroups.hostgroup_id','=','nagios_hostgroup_members.hostgroup_id')
        ->select('nagios_hostgroups.alias as boxgroup_name','nagios_hostgroups.hostgroup_id')
        ->first();
        
        $members = DB::table('nagios_hostgroups')
        ->where('nagios_hostgroups.hostgroup_id', $boxgroup_id)
        ->join('nagios_hostgroup_members','nagios_hostgroups.hostgroup_id','=','nagios_hostgroup_members.hostgroup_id')
        ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
        ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
        ->get();

        $boxes = DB::table('nagios_hosts')
        ->where('alias','box')
        ->select('nagios_hosts.host_object_id','nagios_hosts.display_name as box_name')
        ->get();
        
        $all_members = [];

        foreach ($members as $member) {
            array_push($all_members, $member->host_object_id);    
        }

        return view('config.groups.editBG', compact('boxgroup', 'all_members', 'boxes'));
    }
}
