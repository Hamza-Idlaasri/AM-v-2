<?php

namespace App\Http\Controllers\Config\Edit\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Equipgroup extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function editEG(Request $request,$equipgroup_id)
    {
        // validation
        $this->validate($request,[
            'equipgroup_name' => 'required',
            'members' => 'required',
        ],[
            'members.required' => 'Please check hosts members for your equipgroup',
        ]);

        $members = [];

        foreach ($request->members as $member) {
            
            $element = DB::table('nagios_services')
            ->where('service_object_id', $member)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name')
            ->get();

            array_push($members, $element[0]->box_name);
            array_push($members, $element[0]->equip_name);
        }

        $define_servicegroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$request->equipgroup_name."\n\talias\t\t\t\t".$request->equipgroup_name."\n\tmembers\t\t\t\t".implode(',',$members)."\n}\n";

        $path = "/usr/local/nagios/etc/objects/equipgroups";

        $old_equipgroup = DB::table('nagios_servicegroups')
        ->where('nagios_servicegroups.servicegroup_id', $equipgroup_id)
        ->select('nagios_servicegroups.alias as equipgroup_name')
        ->get();

        file_put_contents($path."/".$old_equipgroup[0]->equipgroup_name.'.cfg', $define_servicegroup);

        if ($old_equipgroup[0]->equipgroup_name != $request->equipgroup_name) {

            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=".$path."/".$old_equipgroup[0]->equipgroup_name.".cfg", "cfg_file=".$path."/".$request->equipgroup_name.".cfg", $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

            rename($path."/".$old_equipgroup[0]->equipgroup_name.'.cfg', $path."/".$request->equipgroup_name.'.cfg');
        }

        shell_exec('sudo service nagios restart');

        return redirect('/config/equipgroups');
    }

}
