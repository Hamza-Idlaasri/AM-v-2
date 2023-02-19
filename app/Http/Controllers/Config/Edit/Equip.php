<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Equip extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function editEquip(Request $request, $equip_object_id)
    {
        // validation
        $this->validate($request,[
            
            // 'equipName' => 'required|min:2|max:20|unique:nagios_services,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'equipName' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
            'check_interval' => 'required|min:1|max:20',
            'retry_interval' => 'required|min:1|max:1200',
            'max_attempts' => 'required|min:1|max:100',
            'notif_interval' => 'required|min:1|max:1000',
            // 'inputNbr' => 'required',
        ]);

        $old_equip_details = DB::table('nagios_services')
            ->where('nagios_services.service_object_id', $equip_object_id)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name','nagios_services.*','nagios_servicestatus.check_command')
            ->first();

        $define_service = "define service {\n\tuse\t\t\t\t\tbox-service\n\thost_name\t\t\t\t".$old_equip_details->box_name."\n\tservice_description\t\t\t".$request->equipName."\n\tcheck_command\t\t\t\t".$old_equip_details->check_command;

        // Normal Check Interval
        if($old_equip_details->check_interval != $request->check_interval)
            $define_service = $define_service."\n\tcheck_interval\t\t\t\t".$request->check_interval;
        
        // Retry Check Interval
            // Convert Time
        $request->retry_interval = floatval(round($request->retry_interval/60,2));
            // Check Time
        if($old_equip_details->retry_interval != $request->retry_interval)
            $define_service = $define_service."\n\tretry_interval\t\t\t\t".$request->retry_interval;

        // Max Check Attempts
        if($old_equip_details->max_check_attempts != $request->max_attempts)
            $define_service = $define_service."\n\tmax_check_attempts\t\t\t".$request->max_attempts;
        
        // Notification Interval
        if($old_equip_details->notification_interval != $request->notif_interval)
            $define_service = $define_service."\n\tnotification_interval\t\t\t".$request->notif_interval;

        // Check this host
        if($request->query('check_it'))
            $define_service = $define_service."\n\tactive_checks_enabled\t\t\t".$request->query('check_it');
        
        // Enable notifications
        if($request->query('active_notif'))
            $define_service = $define_service."\n\tnotifications_enabled\t\t\t".$request->query('active_notif');

        $define_service = $define_service."\n}\n\n";

        if($old_equip_details->equip_name == $request->equipName)
        {
            $path = "/usr/local/nagios/etc/objects/boxes/".$old_equip_details->box_name."/".$request->equipName.".cfg";

            $file = fopen($path, 'w');

            fwrite($file, $define_service);

            fclose($file);

        } else {

            $path = "/usr/local/nagios/etc/objects/boxes/".$old_equip_details->box_name."/".$old_equip_details->equip_name.".cfg";

            $file = fopen($path, 'w');

            fwrite($file, $define_service);

            fclose($file);

            rename("/usr/local/nagios/etc/objects/boxes/".$old_equip_details->box_name."/".$old_equip_details->equip_name.".cfg", "/usr/local/nagios/etc/objects/boxes/".$old_equip_details->box_name."/".$request->equipName.".cfg");

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace($old_equip_details->equip_name, $request->equipName, $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
        }

        //-------------------------------- Edit equip name on equipgroups --------------------------------------------// 
        $equipgroup_member_on =  DB::table('nagios_servicegroup_members')
            ->where('nagios_servicegroup_members.service_object_id',$equip_object_id)
            ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
            ->select('nagios_servicegroups.alias as equipgroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name')
            ->get();
        
        $groups = [];

        foreach ($equipgroup_member_on as $group) {
            
            $equipgroup_members =  DB::table('nagios_servicegroup_members')
                ->join('nagios_services','nagios_servicegroup_members.service_object_id','=','nagios_services.service_object_id')
                ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_servicegroups','nagios_servicegroup_members.servicegroup_id','=','nagios_servicegroups.servicegroup_id')
                ->where('nagios_servicegroups.servicegroup_object_id',$group->servicegroup_object_id)
                ->select('nagios_servicegroups.alias as equipgroup_name','nagios_servicegroups.servicegroup_object_id','nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name')
                ->get();

            $members = [];

            foreach ($equipgroup_members as $member) {
                array_push($members,$member->box_name.",".$member->equip_name);
            }

            array_push($groups,['equipgroup_name' => $group->equipgroup_name,'members' => $members]);

        }

        for ($i=0; $i < sizeof($groups); $i++) {

            $groups[$i]['members'] = str_replace($old_equip_details->box_name.",".$old_equip_details->equip_name,$old_equip_details->box_name.",".$request->equipName, $groups[$i]['members']);

            if (sizeof($groups[$i]['members'])) {
            
                // Editing in servicegroup file
                $path = "/usr/local/nagios/etc/objects/equipgroups/".$groups[$i]['equipgroup_name'].".cfg";  

                $define_equipgroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$groups[$i]['equipgroup_name']."\n\talias\t\t\t\t".$groups[$i]['equipgroup_name']."\n\tmembers\t\t\t\t".implode(',',$groups[$i]['members'])."\n}\n";
            
                $file = fopen($path, 'w');

                fwrite($file, $define_equipgroup);
        
                fclose($file);

            }
            
        }

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('config-equips');
    }
}
