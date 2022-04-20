<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function deleteService($service_object_id)
    {
        $service_deleted = DB::table('nagios_services')
            ->where('nagios_services.service_object_id',$service_object_id)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name')
            ->get();

        $path = "/usr/local/nagios/etc/objects/hosts/".$service_deleted[0]->host_name."/".$service_deleted[0]->service_name.".cfg";

        if (is_file($path)) 
        {
            unlink($path);

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/hosts/{$service_deleted[0]->host_name}/{$service_deleted[0]->service_name}.cfg", '', $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

        } else
            return 'WORNING: No service found';
        
        $service_group_member_on =  DB::table('nagios_servicegroup_members')
            ->where('nagios_servicegroup_members.service_object_id',$service_object_id)
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
            ->select('nagios_servicegroups.alias as servicegroup_name','nagios_services.display_name as service_name','nagios_hosts.display_name as host_name')
            ->first();
        
        if($service_group_member_on)
        {
            $servicegroup_content = file_get_contents("/usr/local/nagios/etc/objects/servicegroups/".$service_group_member_on->servicegroup_name.".cfg");
            $servicegroup_content = str_replace($service_group_member_on->host_name.','.$service_group_member_on->service_name,'', $servicegroup_content);
            file_put_contents("/usr/local/nagios/etc/objects/servicegroups/".$service_group_member_on->servicegroup_name.".cfg",$servicegroup_content);
        }

        shell_exec('sudo service nagios restart');
        
        return redirect()->route('config-services');
        
    }
}
