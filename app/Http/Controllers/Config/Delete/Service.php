<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function deleteService($service_object_id)
    {
        $service_deleted = DB::table('nagios_services')
            ->where('nagios_services.service_object_id',$service_object_id)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varname','SITE')
            ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_customvariables.varvalue as site_name')
            ->first();

        $path = "/usr/local/nagios/etc/objects/hosts/{$service_deleted->site_name}/".$service_deleted->host_name."/".$service_deleted->service_name.".cfg";

        if (is_file($path)) 
        {
            unlink($path);

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/hosts/{$service_deleted->site_name}/{$service_deleted->host_name}/{$service_deleted->service_name}.cfg", '', $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

        } else
            return 'WORNING: No service found';
        
        //------------------------------------------------ Remove from servicegroup -----------------------------------------------------//
        $servicegroup_member_on =  DB::table('nagios_servicegroup_members')
            ->where('nagios_servicegroup_members.service_object_id',$service_object_id)
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
            ->select('nagios_servicegroups.alias as servicegroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as service_name','nagios_hosts.display_name as host_name')
            ->get();
        
        $groups = [];

        foreach ($servicegroup_member_on as $group) {
            
            $servicegroup_members =  DB::table('nagios_servicegroup_members')
                ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
                ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
                ->where('nagios_servicegroups.servicegroup_object_id',$group->servicegroup_object_id)
                ->select('nagios_servicegroups.alias as servicegroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as service_name','nagios_hosts.display_name as host_name')
                ->get();

            $members = [];

            foreach ($servicegroup_members as $member) {
                array_push($members,$member->host_name.",".$member->service_name);
            }

            array_push($groups,['servicegroup_name' => $group->servicegroup_name,'members' => $members]);

        }

        // Remove hostname from hostgroups members
        for ($i=0; $i < sizeof($groups); $i++) { 
            if (($key = array_search($service_deleted->host_name.",".$service_deleted->service_name, $groups[$i]['members'])) !== false) {
                unset($groups[$i]['members'][$key]);
                $groups[$i]['members'] = array_values($groups[$i]['members']);
            }

            if (sizeof($groups[$i]['members'])) {
             
                // Editing in servicegroup file
                $path = "/usr/local/nagios/etc/objects/servicegroups/".$groups[$i]['servicegroup_name'].".cfg";  

                $define_servicegroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$groups[$i]['servicegroup_name']."\n\talias\t\t\t\t".$groups[$i]['servicegroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_servicegroup);
        
                fclose($file);

            }
            else{
                // Editing in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/servicegroups/{$groups[$i]['servicegroup_name']}.cfg", '', $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
             
                // Remove servicegroup file
                unlink("/usr/local/nagios/etc/objects/servicegroups/".$groups[$i]['servicegroup_name'].".cfg");
            }
        }        

        // Restart Nagios 
        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');
        
        return redirect()->route('config-services');
        
    }
}
