<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Box extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function editBox(Request $request, $box_id)
    {
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
            ->where('host_id', $box_id)
            ->get();

        $equips = DB::table('nagios_hosts')
            ->where('host_id', $box_id)
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_services.display_name as equip_name')
            ->get();

        // Parent relationship
        if($request->input('boxes'))
            $define_host = "define host {\n\tuse\t\t\t\t\tlinux-server\n\thost_name\t\t".$request->boxName."\n\talias\t\t\tbox\n\taddress\t\t\t".$request->addressIP."\n\tparents\t\t\t".$request->input('boxes');
        else
            $define_host = "define host {\n\tuse\t\t\t\t\tlinux-server\n\thost_name\t\t\t\t".$request->boxName."\n\talias\t\t\t\t\tbox\n\taddress\t\t\t\t\t".$request->addressIP;

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
    
            }

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace($old_box_details[0]->display_name, $request->boxName, $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
        }

        shell_exec('sudo service nagios restart');

        return redirect()->route('config-boxes');
    }
}
