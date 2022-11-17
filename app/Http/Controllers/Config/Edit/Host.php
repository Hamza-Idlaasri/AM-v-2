<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Host extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function editHost(Request $request, $host_object_id)
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        // validation
        $this->validate($request,[

            'hostName' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
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
            ->first();
        
        $services = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $host_object_id)
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_services.display_name as service_name')
            ->get();

        // Parent relationship
        if($request->input('hosts'))
            $define_host = "define host {\n\tuse\t\t\t\t\tlinux-server\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\tparents\t\t\t".$request->input('hosts')."\n\t_site\t\t\t".$site_name;
        else
            $define_host = "define host {\n\tuse\t\t\t\t\tlinux-server\n\thost_name\t\t\t\t".$request->hostName."\n\talias\t\t\t\t\thost\n\taddress\t\t\t\t\t".$request->addressIP."\n\t_site\t\t\t".$site_name;

        // Normal Check Interval
        if($old_host_details->check_interval != $request->check_interval)
            $define_host = $define_host."\n\tcheck_interval\t\t\t\t".$request->check_interval;
        
        // Retry Check Interval
            // Convert Time
        $request->retry_interval = floatval(round($request->retry_interval/60.2));
            // Check Time
        if($old_host_details->retry_interval != $request->retry_interval)
            $define_host = $define_host."\n\tretry_interval\t\t\t\t".$request->retry_interval;

        // Max Check Attempts
        if($old_host_details->max_check_attempts != $request->max_attempts)
            $define_host = $define_host."\n\tmax_check_attempts\t\t\t".$request->max_attempts;
        
        // Notification Interval
        if($old_host_details->notification_interval != $request->notif_interval)
            $define_host = $define_host."\n\tnotification_interval\t\t\t".$request->notif_interval;

        // Check this host
        if($request->query('check'))
            $define_host = $define_host."\n\tactive_checks_enabled\t\t\t".$request->query('check');
        
        // Enable notifications
        if($request->query('active_notif'))
            $define_host = $define_host."\n\tnotifications_enabled\t\t\t".$request->query('active_notif');


        $define_host = $define_host."\n}\n\n";

        if($old_host_details->display_name == $request->hostName) {

            $path = "/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$request->hostName.".cfg";  
            
            $file = fopen($path, 'w');

            fwrite($file, $define_host);
    
            fclose($file);

        } else {

            $path = "/usr/local/nagios/etc/objects/hosts/".$old_host_details->display_name."/".$old_host_details->display_name.".cfg";
            
            file_put_contents($path, $define_host);

            rename("/usr/local/nagios/etc/objects/hosts/".$old_host_details->display_name."/".$old_host_details->display_name.".cfg", "/usr/local/nagios/etc/objects/hosts/".$old_host_details->display_name."/".$request->hostName.".cfg");

            rename("/usr/local/nagios/etc/objects/hosts/".$old_host_details->display_name, "/usr/local/nagios/etc/objects/hosts/".$request->hostName);
          
            foreach ($services as $service) {
            
                $content = file_get_contents("/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$service->service_name.".cfg");
                $content = str_replace($old_host_details->display_name, $request->hostName, $content);
                file_put_contents("/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$service->service_name.".cfg", $content);

                // Editing in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("/usr/local/nagios/etc/objects/hosts/".$old_host_details->display_name."/".$service->service_name.".cfg", "/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$service->service_name.".cfg", $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
    
            }

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("/usr/local/nagios/etc/objects/hosts/".$old_host_details->display_name."/".$old_host_details->display_name.".cfg", "/usr/local/nagios/etc/objects/hosts/".$request->hostName."/".$request->hostName.".cfg", $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

        }

        //---------------------------------------- Edit host_name on hostgroups -----------------------------//

        $hostgroup_member_on = DB::table('nagios_hostgroup_members')
            ->where('nagios_hostgroup_members.host_object_id',$host_object_id)
            ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_hostgroups','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
            ->select('nagios_hostgroups.alias as hostgroup_name','nagios_hostgroups.hostgroup_object_id','nagios_hosts.display_name as host_name')
            ->get();

        $groups = [];

        foreach ($hostgroup_member_on as $hostgroup) {
               
            $hostgroup_members = DB::table('nagios_hostgroup_members')
                ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_hostgroups','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
                ->select('nagios_hostgroups.alias as hostgroup_name','nagios_hostgroups.hostgroup_object_id','nagios_hosts.display_name as host_name')
                ->where('nagios_hostgroups.hostgroup_object_id', $hostgroup->hostgroup_object_id)
                ->get();

            $members = [];

            foreach ($hostgroup_members as $member) {
                array_push($members, $member->host_name);
            }

            array_push($groups,['hostgroup_name' => $hostgroup->hostgroup_name,'members' => $members]);
        }

        $old_groups = $groups;

        for ($i=0; $i < sizeof($groups); $i++) { 

            $groups[$i]['members'] = str_replace($old_host_details->display_name,$request->hostName,$groups[$i]['members']);

            if (sizeof($groups[$i]['members'])) {
                
                // Editing in hostgroups file
                $path = "/usr/local/nagios/etc/objects/hostgroups/".$groups[$i]['hostgroup_name'].".cfg";  

                $define_hostgroup = "\ndefine hostgroup {\n\thostgroup_name\t\t".$groups[$i]['hostgroup_name']."\n\talias\t\t\t\t".$groups[$i]['hostgroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_hostgroup);
        
                fclose($file);

            }
            
        }
        
        //--------------------------------------- Edit hostname on servicegroup files --------------------------------//
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
                $groups[$i]['members'] = str_replace($old_host_details->display_name.',',$request->hostName.',', $groups[$i]['members']);
            }

            if (sizeof($groups[$i]['members'])) {

                // Editing in equipgroup file
                $path = "/usr/local/nagios/etc/objects/servicegroups/".$groups[$i]['servicegroup_name'].".cfg";  

                $define_servicegroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$groups[$i]['servicegroup_name']."\n\talias\t\t\t\t".$groups[$i]['servicegroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_servicegroup);
        
                fclose($file);

            }

        }

        //--------------------------------- Remove the Host as parrent of another Host -------------------------------//
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
