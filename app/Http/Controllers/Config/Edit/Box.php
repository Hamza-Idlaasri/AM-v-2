<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Box extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function editBox(Request $request, $box_object_id)
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        // validation
        $this->validate($request,[

            // 'boxName' => 'required|min:2|max:20|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'boxName' => 'required|min:2|max:20|regex:/^[a-zA-Z0-9-_+ ]/',
            'addressIP' => 'required|min:7|max:15',
            'check_interval' => 'required|min:1|max:100',
            'retry_interval' => 'required|min:1|max:100',
            'max_attempts' => 'required|min:1|max:100',
            'notif_interval' => 'required|min:1|max:1000'

        ],[
            'addressIP.required' => 'the IP address field is empty',
        ]);

        $old_box_details = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $box_object_id)
            ->get();

        $equips = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $box_object_id)
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_services.display_name as equip_name')
            ->get();

        // Parent relationship
        if($request->input('boxes'))
            $define_host = "define host {\n\tuse\t\t\t\t\tlinux-server\n\thost_name\t\t".$request->boxName."\n\talias\t\t\tbox\n\taddress\t\t\t".$request->addressIP."\n\tparents\t\t\t".$request->input('boxes')."\n\t_site\t\t\t".$site_name;
        else
            $define_host = "define host {\n\tuse\t\t\t\t\tlinux-server\n\thost_name\t\t\t\t".$request->boxName."\n\talias\t\t\t\t\tbox\n\taddress\t\t\t\t\t".$request->addressIP."\n\t_site\t\t\t".$site_name;

        // Normal Check Interval
        if($old_box_details[0]->check_interval != $request->check_interval)
            $define_host = $define_host."\n\tcheck_interval\t\t\t\t".$request->check_interval;
        
        // Retry Check Interval
        if($old_box_details[0]->retry_interval != $request->retry_interval)
            $define_host = $define_host."\n\tretry_interval\t\t\t\t".$request->retry_interval;

        // Max Check Attempts
        if($old_box_details[0]->max_check_attempts != $request->max_attempts)
            $define_host = $define_host."\n\tmax_check_attempts\t\t\t".$request->max_attempts;
        
        // Notification Interval
        if($old_box_details[0]->notification_interval != $request->notif_interval)
            $define_host = $define_host."\n\tnotification_interval\t\t\t".$request->notif_interval;

        // Check this host
        if($request->query('check'))
            $define_host = $define_host."\n\tactive_checks_enabled\t\t\t".$request->query('check');
        
        // Enable notifications
        if($request->query('active_notif'))
            $define_host = $define_host."\n\tnotifications_enabled\t\t\t".$request->query('active_notif');
            
        $define_host = $define_host."\n}\n\n";

        if($old_box_details[0]->display_name == $request->boxName) {

            $path = "/usr/local/nagios/etc/objects/boxes/".$request->boxName."/".$request->boxName.".cfg";  
            
            file_put_contents($path, $define_host);

        } else {

            $path = "/usr/local/nagios/etc/objects/boxes/".$old_box_details[0]->display_name."/".$old_box_details[0]->display_name.".cfg";
            
            file_put_contents($path, $define_host);

            rename("/usr/local/nagios/etc/objects/boxes/".$old_box_details[0]->display_name."/".$old_box_details[0]->display_name.".cfg", "/usr/local/nagios/etc/objects/boxes/".$old_box_details[0]->display_name."/".$request->boxName.".cfg");

            rename("/usr/local/nagios/etc/objects/boxes/".$old_box_details[0]->display_name, "/usr/local/nagios/etc/objects/boxes/".$request->boxName);

            foreach ($equips as $equip) {
            
                $content = file_get_contents("/usr/local/nagios/etc/objects/boxes/".$request->boxName."/".$equip->equip_name.".cfg");
                $content = str_replace($old_box_details[0]->display_name, $request->boxName, $content);
                file_put_contents("/usr/local/nagios/etc/objects/boxes/".$request->boxName."/".$equip->equip_name.".cfg", $content);

                // Editing in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("/usr/local/nagios/etc/objects/boxes/".$old_box_details[0]->display_name."/".$service->equip_name.".cfg", "/usr/local/nagios/etc/objects/boxes/".$request->boxName."/".$service->equip_name.".cfg", $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
    
            }

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("/usr/local/nagios/etc/objects/boxes/".$old_box_details[0]->display_name."/".$old_box_details[0]->display_name.".cfg", "/usr/local/nagios/etc/objects/boxes/".$request->boxName."/".$request->boxName.".cfg", $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
        }

        $boxgroup_member_on = DB::table('nagios_hostgroup_members')
            ->where('nagios_hostgroup_members.host_object_id',$box_object_id)
            ->join('nagios_hosts','nagios_hostgroup_members.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_hostgroups','nagios_hostgroup_members.hostgroup_id','=','nagios_hostgroups.hostgroup_id')
            ->select('nagios_hostgroups.alias as boxgroup_name','nagios_hostgroups.hostgroup_object_id','nagios_hosts.display_name as box_name')
            ->first();

        if($boxgroup_member_on)
        {
            $boxgroup_content = file_get_contents("/usr/local/nagios/etc/objects/boxgroups/".$boxgroup_member_on->boxgroup_name.".cfg");
            $boxgroup_content = str_replace("members\t\t\t".$boxgroup_member_on->box_name, "members\t\t\t".$request->boxName, $boxgroup_content);
            file_put_contents("/usr/local/nagios/etc/objects/boxgroups/".$boxgroup_member_on->boxgroup_name.".cfg",$boxgroup_content);
        }

        $equipgroups = DB::table('nagios_servicegroups')->get();

        if(sizeof($equipgroups))
        {
            foreach ($equipgroups as $equipgroup) {

                $path = "/usr/local/nagios/etc/objects/equipgroups/".$equipgroup->alias.".cfg";

                if(file_exists($path))
                {
                    $equipgroup_content = file_get_contents($path);
                    $equipgroup_content = str_replace($old_box_details[0]->display_name, $request->boxName, $equipgroup_content);
                    file_put_contents($path,$equipgroup_content);
                }
            }
        }

        // Remove the Host as parrent of another Host
        $parent_host = DB::table('nagios_host_parenthosts')
            ->where('nagios_host_parenthosts.parent_host_object_id',$box_object_id)
            ->join('nagios_hosts','nagios_host_parenthosts.host_id','=','nagios_hosts.host_id')
            ->select('nagios_hosts.display_name as box_name','nagios_hosts.alias as host_type')
            ->get();

        foreach ($parent_host as $host) {

            if($host->host_type == 'host')
            {
                $directory = "hosts";
            }

            if ($host->host_type == 'box') {
                $directory = "boxes";
            }

            $myFile = "/usr/local/nagios/etc/objects/".$directory."/".$host->box_name."/".$host->box_name.".cfg";
            $lines = file($myFile);
            $parents_line = $lines[5];

            // Editing in host .cfg file
            $host_file_content = file_get_contents("/usr/local/nagios/etc/objects/".$directory."/".$host->box_name."/".$host->box_name.".cfg");
            $host_file_content = str_replace($lines[5], "\tparents\t\t\t".$request->boxName."\n", $host_file_content);
            file_put_contents("/usr/local/nagios/etc/objects/".$directory."/".$host->box_name."/".$host->box_name.".cfg", $host_file_content);
        
        }

        shell_exec('sudo service nagios restart');

        return redirect()->route('config-boxes');
    }
}
