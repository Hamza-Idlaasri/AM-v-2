<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Equip extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function deleteEquip($equip_object_id)
    {
        $equip_deleted = DB::table('nagios_services')
            ->where('nagios_services.service_object_id',$equip_object_id)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name')
            ->get();

        $path = "/usr/local/nagios/etc/objects/boxes/".$equip_deleted[0]->box_name."/".$equip_deleted[0]->equip_name.".cfg";

        if (is_file($path)) 
        {
            unlink($path);

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/boxes/{$equip_deleted[0]->box_name}/{$equip_deleted[0]->equip_name}.cfg", '', $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
            
        } else
            return 'WORNING: No equipment found';
        
        $equip_group_member_on =  DB::table('nagios_servicegroup_members')
            ->where('nagios_servicegroup_members.service_object_id',$equip_object_id)
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
            ->select('nagios_servicegroups.alias as equipgroup_name','nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name')
            ->first();
        
        if($equip_group_member_on)
        {
            $equipgroup_content = file_get_contents("/usr/local/nagios/etc/objects/equipgroups/".$equip_group_member_on->equipgroup_name.".cfg");
            $equipgroup_content = str_replace($equip_group_member_on->box_name.','.$equip_group_member_on->equip_name, '', $equipgroup_content);
            file_put_contents("/usr/local/nagios/etc/objects/equipgroups/".$equip_group_member_on->equipgroup_name.".cfg",$equipgroup_content);
        }

        shell_exec('sudo service nagios restart');

        return redirect()->route('config-equips');
    }
}
