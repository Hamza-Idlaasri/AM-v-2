<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function editService(Request $request, $service_object_id)
    {
        // validation
        $this->validate($request,[
         
            'serviceName' => 'required',
            'check_interval' => 'required|min:1|max:100',
            'retry_interval' => 'required|min:1|max:100',
            'max_attempts' => 'required|min:1|max:100',
            'notif_interval' => 'required|min:1|max:1000',
        
        ]);

        $old_service_details = DB::table('nagios_services')
            ->where('nagios_services.service_object_id', $service_object_id)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_services.*','nagios_servicestatus.check_command')
            ->first();

        $define_service = "define service {\n\tuse\t\t\t\t\tgeneric-service\n\thost_name\t\t\t\t".$old_service_details->host_name."\n\tservice_description\t\t\t".$request->serviceName."\n\tcheck_command\t\t\t\t".$old_service_details->check_command;

        // Normal Check Interval
        if($old_service_details->check_interval != $request->check_interval)
            $define_service = $define_service."\n\tcheck_interval\t\t\t\t".$request->check_interval;
        
        // Retry Check Interval
        if($old_service_details->retry_interval != $request->retry_interval)
            $define_service = $define_service."\n\tretry_interval\t\t\t\t".$request->retry_interval;

        // Max Check Attempts
        if($old_service_details->max_check_attempts != $request->max_attempts)
            $define_service = $define_service."\n\tmax_check_attempts\t\t\t".$request->max_attempts;
        
        // Notification Interval
        if($old_service_details->notification_interval != $request->notif_interval)
            $define_service = $define_service."\n\tnotification_interval\t\t\t".$request->notif_interval;

        // Check this host
        if($request->query('check'))
            $define_service = $define_service."\n\tactive_checks_enabled\t\t\t".$request->query('check');
        
        // Enable notifications
        if($request->query('active_notif'))
            $define_service = $define_service."\n\tnotifications_enabled\t\t\t".$request->query('active_notif');

        $define_service = $define_service."\n}\n\n";

        if($old_service_details->service_name == $request->serviceName)
        {
            $path = "/usr/local/nagios/etc/objects/hosts/".$old_service_details->host_name."/".$request->serviceName.".cfg";

            file_put_contents($path, $define_service);

        } else {

            $path = "/usr/local/nagios/etc/objects/hosts/".$old_service_details->host_name."/".$old_service_details->service_name.".cfg";

            file_put_contents($path, $define_service);

            rename("/usr/local/nagios/etc/objects/hosts/".$old_service_details->host_name."/".$old_service_details->service_name.".cfg", "/usr/local/nagios/etc/objects/hosts/".$old_service_details->host_name."/".$request->serviceName.".cfg");

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace($old_service_details->service_name, $request->serviceName, $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
        }

        //------------------------------------ Edit on servicegroups belong to --------------------------------------//
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

            $groups[$i]['members'] = str_replace($old_service_details->host_name.",".$old_service_details->service_name,$old_service_details->host_name.",".$request->serviceName, $groups[$i]['members']);

            if (sizeof($groups[$i]['members'])) {
             
                // Editing in servicegroup file
                $path = "/usr/local/nagios/etc/objects/servicegroups/".$groups[$i]['servicegroup_name'].".cfg";  

                $define_servicegroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$groups[$i]['servicegroup_name']."\n\talias\t\t\t\t".$groups[$i]['servicegroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_servicegroup);
        
                fclose($file);

            }
            
        }   

        shell_exec('sudo service nagios restart');

        return redirect()->route('config-services');
        
    }
}
