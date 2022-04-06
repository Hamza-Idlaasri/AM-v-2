<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Equip extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function editEquip(Request $request, $equip_id)
    {
        // validation
        $this->validate($request,[
            
            // 'equipName' => 'required|min:2|max:20|unique:nagios_services,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'equipName' => 'required|min:2|max:20|regex:/^[a-zA-Z0-9-_+ ]/',
            'check_interval' => 'required|min:1|max:100',
            'retry_interval' => 'required|min:1|max:100',
            'max_attempts' => 'required|min:1|max:100',
            'notif_interval' => 'required|min:1|max:1000',
            // 'inputNbr' => 'required',
        ]);

        $old_equip_details = DB::table('nagios_services')
            ->where('service_id', $equip_id)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_services.*','nagios_servicestatus.check_command')
            ->get();

        $define_service = "define service {\n\tuse\t\t\t\t\tbox-service\n\thost_name\t\t\t\t".$old_equip_details[0]->host_name."\n\tservice_description\t\t\t".$request->equipName."\n\tcheck_command\t\t\t\t".$old_equip_details[0]->check_command;

        // Normal Check Interval
        if($old_equip_details[0]->check_interval != $request->check_interval)
            $define_service = $define_service."\n\tcheck_interval\t\t\t\t".$request->check_interval;
        
        // Retry Check Interval
        if($old_equip_details[0]->retry_interval != $request->retry_interval)
            $define_service = $define_service."\n\tretry_interval\t\t\t\t".$request->retry_interval;

        // Max Check Attempts
        if($old_equip_details[0]->max_check_attempts != $request->max_attempts)
            $define_service = $define_service."\n\tmax_check_attempts\t\t\t".$request->max_attempts;
        
        // Notification Interval
        if($old_equip_details[0]->notification_interval != $request->notif_interval)
            $define_service = $define_service."\n\tnotification_interval\t\t\t".$request->notif_interval;

        // Check this host
        if($request->query('check_it'))
            $define_service = $define_service."\n\tactive_checks_enabled\t\t\t".$request->query('check_it');
        
        // Enable notifications
        if($request->query('active_notif'))
            $define_service = $define_service."\n\tnotifications_enabled\t\t\t".$request->query('active_notif');

        $define_service = $define_service."\n}\n\n";

        if($old_equip_details[0]->service_name == $request->equipName)
        {
            $path = "/usr/local/nagios/etc/objects/boxs/".$old_equip_details[0]->host_name."/".$request->equipName.".cfg";

            $file = fopen($path, 'w');

            fwrite($file, $define_service);

            fclose($file);

        } else {

            $path = "/usr/local/nagios/etc/objects/boxs/".$old_equip_details[0]->host_name."/".$old_equip_details[0]->service_name.".cfg";

            $file = fopen($path, 'w');

            fwrite($file, $define_service);

            fclose($file);

            rename("/usr/local/nagios/etc/objects/boxs/".$old_equip_details[0]->host_name."/".$old_equip_details[0]->service_name.".cfg", "/usr/local/nagios/etc/objects/boxs/".$old_equip_details[0]->host_name."/".$request->equipName.".cfg");

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace($old_equip_details[0]->display_name, $request->equipName, $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
        }

        shell_exec('sudo service nagios restart');

        return redirect()->route('configEquips');
    }
}
