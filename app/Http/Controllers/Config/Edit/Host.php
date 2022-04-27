<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Host extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function editHost(Request $request, $host_object_id)
    {
        // validation
        $this->validate($request,[

            'hostName' => 'required|min:2|max:20|regex:/^[a-zA-Z0-9-_+ ]/',
            'addressIP' => 'required|min:7|max:15|regex:/^[0-9.]/',
            'check_interval' => 'required|min:1|max:100',
            'retry_interval' => 'required|min:1|max:100',
            'max_attempts' => 'required|min:1|max:100',
            'notif_interval' => 'required|min:1|max:1000',

        ],[
            'addressIP.required' => 'the IP address field is empty',
        ]);
        
        $old_host_details = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $host_object_id)
            ->get();
        
        $services = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $host_object_id)
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_services.display_name as service_name')
            ->get();

        
        // Parent relationship
        if($request->input('hosts'))
            $define_host = "define host {\n\tuse\t\t\t\t\tlinux-server\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\tparents\t\t\t".$request->input('hosts');
        else
            $define_host = "define host {\n\tuse\t\t\t\t\tlinux-server\n\thost_name\t\t\t\t".$request->hostName."\n\talias\t\t\t\t\thost\n\taddress\t\t\t\t\t".$request->addressIP;

        // Normal Check Interval
        if($old_host_details[0]->check_interval != $request->check_interval)
            $define_host = $define_host."\n\tcheck_interval\t\t\t\t".$request->check_interval;
        
        // Retry Check Interval
        if($old_host_details[0]->retry_interval != $request->retry_interval)
            $define_host = $define_host."\n\tretry_interval\t\t\t\t".$request->retry_interval;

        // Max Check Attempts
        if($old_host_details[0]->max_check_attempts != $request->max_attempts)
            $define_host = $define_host."\n\tmax_check_attempts\t\t\t".$request->max_attempts;
        
        // Notification Interval
        if($old_host_details[0]->notification_interval != $request->notif_interval)
            $define_host = $define_host."\n\tnotification_interval\t\t\t".$request->notif_interval;

        // Check this host
        if($request->query('check'))
            $define_host = $define_host."\n\tactive_checks_enabled\t\t\t".$request->query('check');
        
        // Enable notifications
        if($request->query('active_notif'))
            $define_host = $define_host."\n\tnotifications_enabled\t\t\t".$request->query('active_notif');


        $define_host = $define_host."\n}\n\n";

        if($old_host_details[0]->display_name == $request->hostName) {

            $path = "/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$request->hostName.".cfg";  
            
            $file = fopen($path, 'w');

            fwrite($file, $define_host);
    
            fclose($file);

        } else {

            $path = "/usr/local/nagios/etc/objects/hosts/".$old_host_details[0]->display_name."/".$old_host_details[0]->display_name.".cfg";
            
            file_put_contents($path, $define_host);

            rename("/usr/local/nagios/etc/objects/hosts/".$old_host_details[0]->display_name."/".$old_host_details[0]->display_name.".cfg", "/usr/local/nagios/etc/objects/hosts/".$old_host_details[0]->display_name."/".$request->hostName.".cfg");

            rename("/usr/local/nagios/etc/objects/hosts/".$old_host_details[0]->display_name, "/usr/local/nagios/etc/objects/hosts/".$request->hostName);
          
            foreach ($services as $service) {
            
                $content = file_get_contents("/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$service->service_name.".cfg");
                $content = str_replace($old_host_details[0]->display_name, $request->hostName, $content);
                file_put_contents("/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$service->service_name.".cfg", $content);

                // Editing in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("/usr/local/nagios/etc/objects/hosts/".$old_host_details[0]->display_name."/".$service->service_name.".cfg", "/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$service->service_name.".cfg", $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
    
            }

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("/usr/local/nagios/etc/objects/hosts/".$old_host_details[0]->display_name."/".$old_host_details[0]->display_name.".cfg", "/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$request->hostName.".cfg", $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

        }

        $hostgroup_member_on = DB::table('nagios_hostgroup_members')
            ->where('nagios_hostgroup_members.host_object_id',$host_object_id)
            ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_hostgroups','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
            ->select('nagios_hostgroups.alias as hostgroup_name','nagios_hostgroups.hostgroup_object_id','nagios_hosts.display_name as host_name')
            ->get();

        if(sizeof($hostgroup_member_on))
        {
            foreach ($hostgroup_member_on as $group) {
                $hostgroup_content = file_get_contents("/usr/local/nagios/etc/objects/hostgroups/".$group->hostgroup_name.".cfg");
                $hostgroup_content = str_replace("members\t\t\t".$group->host_name, "members\t\t\t".$request->hostName, $hostgroup_content);
                file_put_contents("/usr/local/nagios/etc/objects/hostgroups/".$group->hostgroup_name.".cfg",$hostgroup_content);
            }
        }
        
        // Edit hostname on servicegroup files
        $servicegroups = DB::table('nagios_servicegroups')->get();

        if(sizeof($servicegroups))
        {
            foreach ($servicegroups as $servicegroup) {

                $path = "/usr/local/nagios/etc/objects/servicegroups/".$servicegroup->alias.".cfg";

                if(file_exists($path))
                {
                    $servicegroup_content = file_get_contents($path);
                    $servicegroup_content = str_replace($old_host_details[0]->display_name, $request->hostName, $servicegroup_content);
                    file_put_contents($path,$servicegroup_content);
                }
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
            $host_file_content = str_replace($lines[5], "\tparents\t\t\t".$request->hostName."\n", $host_file_content);
            file_put_contents("/usr/local/nagios/etc/objects/".$directory."/".$host->host_name."/".$host->host_name.".cfg", $host_file_content);
        
        }

        shell_exec('sudo service nagios restart');

        return redirect()->route('config-hosts');

    }

}
