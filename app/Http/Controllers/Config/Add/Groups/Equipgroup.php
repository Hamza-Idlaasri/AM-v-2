<?php

namespace App\Http\Controllers\Config\Add\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Equipgroup extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    // Add equipementgroup
    public function addEG()
    {
        $equips = DB::table('nagios_hosts')
        ->where('alias','box')
        ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
        ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name','nagios_services.service_object_id')
        ->get();

        return view('config.groups.add.addEG', compact('equips'));
    }

    // Create New equipementGroup
    public function createEG(Request $request)
    {
        // validation
        $this->validate($request,[
            'equipgroup_name' => 'required|min:2|max:200|unique:nagios_servicegroups,alias|regex:/^[a-zA-Z0-9-_+ ]/',
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

        $define_equipgroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$request->equipgroup_name."\n\talias\t\t\t\t".$request->equipgroup_name."\n\tmembers\t\t\t\t".implode(',',$members)."\n}\n";

        $path = "/usr/local/nagios/etc/objects/equipgroups/".$request->equipgroup_name.".cfg";

        file_put_contents($path, $define_equipgroup);
        $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/equipgroups/".$request->equipgroup_name.".cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        shell_exec('sudo service nagios restart');

        return redirect('/config/equipgroups');
    }
}
