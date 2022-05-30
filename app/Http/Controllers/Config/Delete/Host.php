<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Host extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }

    public function deleteHost($host_object_id)
    {
        $host_deleted = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $host_object_id)
            ->select('nagios_hosts.display_name as host_name')
            ->first();

        $host_services = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $host_object_id)
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name')
            ->get();

        $path = "/usr/local/nagios/etc/objects/hosts/".$host_deleted->host_name;

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
            $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/hosts/{$host_deleted->host_name}/{$host_deleted->host_name}.cfg", '', $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

            // Remove host services
            foreach ($host_services as $service) {
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/hosts/{$service->host_name}/{$service->service_name}.cfg", '', $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
            }

        } else {
            return 'WORNING: No host found';
        }

        //------------------------------------------- Remove the Host as member in hostgroups ----------------------------------------------//
        $hostgroup_member_on = DB::table('nagios_hostgroup_members')
            ->where('nagios_hostgroup_members.host_object_id',$host_object_id)
            ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_hostgroups','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
            ->select('nagios_hostgroups.alias as hostgroup_name','nagios_hostgroups.hostgroup_object_id','nagios_hosts.display_name as host_name')
            ->get();

        $groups = [];

        foreach ($hostgroup_member_on as $group) {

            $hostgroup_members = DB::table('nagios_hostgroup_members')
                ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_hostgroups','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
                ->select('nagios_hostgroups.alias as hostgroup_name','nagios_hostgroups.hostgroup_object_id','nagios_hosts.display_name as host_name')
                ->where('nagios_hostgroups.hostgroup_object_id', $group->hostgroup_object_id)
                ->get();

            $members = [];

            foreach ($hostgroup_members as $member) {
                array_push($members, $member->host_name);
            }

            array_push($groups,['hostgroup_name' => $group->hostgroup_name,'members' => $members]);
        }

        // Remove hostname from hostgroups members
        for ($i=0; $i < sizeof($groups); $i++) {

            if (($key = array_search($host_deleted->host_name, $groups[$i]['members'])) !== false) {
                unset($groups[$i]['members'][$key]);
                $groups[$i]['members'] = array_values($groups[$i]['members']);
            }

            if (sizeof($groups[$i]['members'])) {

                // Editing in hostgroup file
                $path = "/usr/local/nagios/etc/objects/hostgroups/".$groups[$i]['hostgroup_name'].".cfg";  

                $define_hostgroup = "\ndefine hostgroup {\n\thostgroup_name\t\t".$groups[$i]['hostgroup_name']."\n\talias\t\t\t\t".$groups[$i]['hostgroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_hostgroup);
        
                fclose($file);

            }
            else {
                // Editing in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/hostgroups/{$groups[$i]['hostgroup_name']}.cfg", '', $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

                // Remove hostgroup file
                unlink("/usr/local/nagios/etc/objects/hostgroups/".$groups[$i]['hostgroup_name'].".cfg");
            }
        }

        //---------------------------------------------- Remove the Host from servicegroups ------------------------------------------------//
        $servicegroups = DB::table('nagios_servicegroup_members')
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
            ->where('nagios_hosts.host_object_id',$host_object_id)
            ->select('nagios_servicegroups.alias as servicegroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as service_name','nagios_hosts.display_name as host_name')
            ->get();

        $groups = [];

        foreach ($servicegroups as $group) {

            $servicegroup_members = DB::table('nagios_servicegroup_members')
                ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
                ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
                ->select('nagios_servicegroups.alias as servicegroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as service_name','nagios_hosts.display_name as host_name')
                ->where('nagios_servicegroups.servicegroup_object_id',$group->servicegroup_object_id)
                ->get();
                    
            $members = [];

            foreach ($servicegroup_members as $member) {
                array_push($members, $member->host_name.','.$member->service_name);
            }

            array_push($groups,['servicegroup_name' => $group->servicegroup_name,'members' => $members]);

        }
        
        $groups = array_values(array_unique($groups, SORT_REGULAR));

        // Remove hostname from hostgroups members
        for ($i=0; $i < sizeof($groups); $i++) {

            foreach ($servicegroups as $servicegroup) {
                if (($key = array_search($servicegroup->host_name.','.$servicegroup->service_name, $groups[$i]['members'])) !== false) {
                    unset($groups[$i]['members'][$key]);
                    $groups[$i]['members'] = array_values($groups[$i]['members']);
                }
            }

            if (sizeof($groups[$i]['members'])) {

                // Editing in servicegroup file
                $path = "/usr/local/nagios/etc/objects/servicegroups/".$groups[$i]['servicegroup_name'].".cfg";  

                $define_hostgroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$groups[$i]['servicegroup_name']."\n\talias\t\t\t\t".$groups[$i]['servicegroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_hostgroup);
        
                fclose($file);

            }
            else {
                // Editing in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/servicegroups/{$groups[$i]['servicegroup_name']}.cfg", '', $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

                // Remove servicegroup file
                unlink("/usr/local/nagios/etc/objects/servicegroups/".$groups[$i]['servicegroup_name'].".cfg");
            }
        }

        // Remove the Host as parrent of another Host
        $parent_host = DB::table('nagios_host_parenthosts')
            ->where('nagios_host_parenthosts.parent_host_object_id',$host_object_id)
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
        
        shell_exec('sudo service nagios restart');

        return redirect()->route('config-hosts');
    }
}
