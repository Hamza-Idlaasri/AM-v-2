<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;

class Box extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function deleteBox($box_object_id)
    {
        $box_deleted = DB::table('nagios_hosts')
            ->where('host_object_id', $box_object_id)
            ->select('nagios_hosts.display_name as box_name')
            ->first();
        
        $box_equips = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $box_object_id)
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name')
            ->get();

        $path = "/usr/local/nagios/etc/objects/boxes/".$box_deleted->box_name;

        if(is_dir($path))
        {
            $objects = scandir($path);

            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    unlink($path. DIRECTORY_SEPARATOR .$object); 
                }
            }

            rmdir($path);

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/boxes/".$box_deleted->box_name."/".$box_deleted->box_name.".cfg", '', $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

            // Remove box equips
            foreach ($box_equips as $equip) {
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/boxes/".$equip->box_name."/".$equip->equip_name.".cfg", '', $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
            }

        } else {
            return 'WORNING: No box found';
        }

        //-------------------------------- Edit name of the box in equips_names & equips_details tables -------------------------//
        
        EquipsDetail::where(['box_name' => $box_deleted->box_name])->delete(); 
        EquipsNames::where(['box_name' => $box_deleted->box_name])->delete(); 

        //-------------------------------------- Remove the Box as member in boxgroups ----------------------------//
        $boxgroup_member_on = DB::table('nagios_hostgroup_members')
            ->where('nagios_hostgroup_members.host_object_id',$box_object_id)
            ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_hostgroups','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
            ->select('nagios_hostgroups.alias as boxgroup_name','nagios_hostgroups.hostgroup_object_id','nagios_hosts.display_name as box_name')
            ->get();

        $groups = [];

        foreach ($boxgroup_member_on as $group) {
               
            $boxgroup_members = DB::table('nagios_hostgroup_members')
                ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_hostgroups','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
                ->select('nagios_hostgroups.alias as boxgroup_name','nagios_hostgroups.hostgroup_object_id','nagios_hosts.display_name as box_name')
                ->where('nagios_hostgroups.hostgroup_object_id', $group->hostgroup_object_id)
                ->get();

            $members = [];

            foreach ($boxgroup_members as $member) {
                array_push($members, $member->box_name);
            }

            array_push($groups,['boxgroup_name' => $group->boxgroup_name,'members' => $members]);
        }

        $old_groups = $groups;

        for ($i=0; $i < sizeof($groups); $i++) { 

            if (($key = array_search($box_deleted->box_name, $groups[$i]['members'])) !== false) {
                unset($groups[$i]['members'][$key]);
                $groups[$i]['members'] = array_values($groups[$i]['members']);
            }

            if (sizeof($groups[$i]['members'])) {
                
                // Editing in equipgroup file
                $path = "/usr/local/nagios/etc/objects/boxgroups/".$groups[$i]['boxgroup_name'].".cfg";  

                $define_boxgroup = "\ndefine hostgroup {\n\thostgroup_name\t\t".$groups[$i]['boxgroup_name']."\n\talias\t\t\t\t".$groups[$i]['boxgroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_boxgroup);
        
                fclose($file);

            }
            else{
                // Editing in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/boxgroups/{$groups[$i]['boxgroup_name']}.cfg", '', $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
                
                // Remove boxgroup file
                unlink("/usr/local/nagios/etc/objects/boxgroups/".$groups[$i]['boxgroup_name'].".cfg");
            }
        }


        //-------------------------------- Remove the box from equipgroups ---------------------------------------//
        $equipgroups = DB::table('nagios_servicegroup_members')
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
            ->where('nagios_hosts.host_object_id',$box_object_id)
            ->select('nagios_servicegroups.alias as equipgroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name')
            ->get();

        $groups = [];

        foreach ($equipgroups as $group) {

            $servicegroup_members = DB::table('nagios_servicegroup_members')
                ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
                ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
                ->select('nagios_servicegroups.alias as equipgroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name')
                ->where('nagios_servicegroups.servicegroup_object_id',$group->servicegroup_object_id)
                ->get();
                    
            $members = [];

            foreach ($servicegroup_members as $member) {
                array_push($members, $member->box_name.','.$member->equip_name);
            }

            array_push($groups,['equipgroup_name' => $group->equipgroup_name,'members' => $members]);

        }
        
        $groups = array_values(array_unique($groups, SORT_REGULAR));

        $old_groups = $groups;

        for ($i=0; $i < sizeof($groups); $i++) {

            foreach ($equipgroups as $servicegroup) {
                if (($key = array_search($servicegroup->box_name.','.$servicegroup->equip_name, $groups[$i]['members'])) !== false) {
                    unset($groups[$i]['members'][$key]);
                    $groups[$i]['members'] = array_values($groups[$i]['members']);
                }
            }

            if (sizeof($groups[$i]['members'])) {

                // Editing in equipgroup file
                $path = "/usr/local/nagios/etc/objects/equipgroups/".$groups[$i]['equipgroup_name'].".cfg";  

                $define_equipgroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$groups[$i]['equipgroup_name']."\n\talias\t\t\t\t".$groups[$i]['equipgroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_equipgroup);
        
                fclose($file);

            }
            else{
                // Editing in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/equipgroups/{$groups[$i]['equipgroup_name']}.cfg", '', $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

                // Remove servicegroup file
                unlink("/usr/local/nagios/etc/objects/equipgroups/".$groups[$i]['equipgroup_name'].".cfg");
            }
        }

        //--------------------------------- Remove the Host as parrent of another Host ------------------------------//
        $parent_host = DB::table('nagios_host_parenthosts')
            ->where('nagios_host_parenthosts.parent_host_object_id',$box_object_id)
            ->join('nagios_hosts','nagios_host_parenthosts.host_id','=','nagios_hosts.host_id')
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.alias as host_type')
            ->get();

        foreach ($parent_host as $host) {

            if($host->host_type == 'host')
            {
                $directory = "hosts";
            }

            if ($host->host_type == 'box') {
                $directory = "boxes";
            }

            $myFile = "/usr/local/nagios/etc/objects/".$directory."/".$host->host_name."/".$host->host_name.".cfg";
            $lines = file($myFile);
            $parents_line = $lines[5];

            // Editing in host .cfg file
            $host_file_content = file_get_contents("/usr/local/nagios/etc/objects/".$directory."/".$host->host_name."/".$host->host_name.".cfg");
            $host_file_content = str_replace($lines[5], '', $host_file_content);
            file_put_contents("/usr/local/nagios/etc/objects/".$directory."/".$host->host_name."/".$host->host_name.".cfg", $host_file_content);
        
        }

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('config-boxes');

    }
}
