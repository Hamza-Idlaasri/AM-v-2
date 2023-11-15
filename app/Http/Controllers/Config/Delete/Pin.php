<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;

class Pin extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function deletePin($pin_object_id)
    {
        $pin_deleted = DB::table('nagios_services')
            ->where('nagios_services.service_object_id', $pin_object_id)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('am.equips_details as ed','nagios_services.display_name','=','ed.pin_name')
            ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as pin_name','ed.equip_name')
            ->first();

        
        $path = "/usr/local/nagios/etc/objects/boxes/".$pin_deleted->box_name."/".$pin_deleted->pin_name.".cfg";

        if (is_file($path)) 
        {
            unlink($path);

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/boxes/{$pin_deleted->box_name}/{$pin_deleted->pin_name}.cfg", '', $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
            
        } else
            return 'WORNING: No equipment found';
             
        //-------------------------------- Delete equip name of the pin name in equips_names table -------------------------------------//

        // $parent_equip = EquipsDetail::where('box_name', $pin_deleted->box_name)->where('equip_name', $pin_deleted->equip_name)->get();

        // if (sizeof($parent_equip) == 1) {
        //     EquipsNames::where(['box_name' => $pin_deleted->box_name])->where(['equip_name' => $pin_deleted->equip_name])->delete();
        // }

        //-------------------------------- Edit pin name in equips_details table -------------------------------------//

        EquipsDetail::where(['box_name' => $pin_deleted->box_name])->where(['pin_name' => $pin_deleted->pin_name])->delete();

        //------------------------------------------- Remove from equipgroup -----------------------------------------//

        $equipgroup_member_on =  DB::table('nagios_servicegroup_members')
            ->where('nagios_servicegroup_members.service_object_id',$pin_object_id)
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
            ->select('nagios_servicegroups.alias as equipgroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name')
            ->get();
        
        $groups = [];

        foreach ($equipgroup_member_on as $group) {
            
            $equipgroup_members =  DB::table('nagios_servicegroup_members')
                ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
                ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
                ->where('nagios_servicegroups.servicegroup_object_id',$group->servicegroup_object_id)
                ->select('nagios_servicegroups.alias as equipgroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name')
                ->get();

            $members = [];

            foreach ($equipgroup_members as $member) {
                array_push($members,$member->box_name.",".$member->equip_name);
            }

            array_push($groups,['equipgroup_name' => $group->equipgroup_name,'members' => $members]);

        }

        $old_groups = $groups;

        // Remove hostname from boxgroups members
        for ($i=0; $i < sizeof($groups); $i++) {
            if (($key = array_search($pin_deleted->box_name.",".$pin_deleted->pin_name, $groups[$i]['members'])) !== false) {
                unset($groups[$i]['members'][$key]);
                $groups[$i]['members'] = array_values($groups[$i]['members']);
            }

            if (sizeof($groups[$i]['members'])) {
             
                // Editing in equipgroup file
                $path = "/usr/local/nagios/etc/objects/equipgroups/".$groups[$i]['equipgroup_name'].".cfg";  

                $define_equipgroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$groups[$i]['equipgroup_name']."\n\talias\t\t\t\t".$groups[$i]['equipgroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_equipgroup);
        
                fclose($file);

                // $equipgroup_file_content = file_get_contents("/usr/local/nagios/etc/objects/equipgroups/".$groups[$i]['equipgroup_name'].".cfg");
                // $equipgroup_file_content = str_replace("members\t\t\t\t".implode(',',$old_groups[$i]['members']),"members\t\t\t\t".implode(',',$groups[$i]['members']), $equipgroup_file_content);
                // file_put_contents("/usr/local/nagios/etc/objects/equipgroups/".$groups[$i]['equipgroup_name'].".cfg", $equipgroup_file_content);
            }
            else{
                // Remove the path of equipgroup in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/equipgroups/{$groups[$i]['equipgroup_name']}.cfg", '', $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
             
                // Remove equipgroup file
                unlink("/usr/local/nagios/etc/objects/equipgroups/".$groups[$i]['equipgroup_name'].".cfg");
            }
        }        

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('config-pins');
    }
}
