<?php

namespace App\Http\Controllers\Config\Add\Equips;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Equip extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function createEquip(Request $request,$box_id)
    {
        // validation
        $this->validate($request,[

            'equipName.*' => 'required|min:2|max:200|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'inputNbr.*' => 'required|min:1|max:10',
         
        ],[
         
            'equipName.*.required' => 'the equipement name field is empty',
            'inputNbr.*.required' => 'the input number field is empty',
        ]);

        $add_to_box = DB::table('nagios_hosts')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_hosts.host_object_id', $box_id)
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varname','BOXTYPE')
            ->select('nagios_hosts.display_name as box_name','nagios_customvariables.varvalue as box_type')
            ->first();

        $equipNames = $request->input('equipName');
        $equiINnbr = $request->input('inputNbr');

        // Define equip
        for ($i=0; $i < sizeof($equipNames); $i++) {

            $define_service = "define service {\n\tuse\t\t\tbox-service\n\thost_name\t\t".$add_to_box->box_name."\n\tservice_description\t".$equipNames[$i]."\n\tcheck_command\t\t".$add_to_box->box_type."_IN".$equiINnbr[$i]."\n}\n\n"; 

            $box_dir = "/usr/local/nagios/etc/objects/boxes/".$add_to_box->box_name."/".$equipNames[$i].".cfg";

            file_put_contents($box_dir, $define_service);

            // Add equip path to nagios.cfg file
            $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/boxes/{$add_to_box->box_name}/{$equipNames[$i]}.cfg";
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        }

        shell_exec('sudo service nagios restart');

        return redirect()->route('monitoring.equips');
        
    }
}
