<?php

namespace App\Http\Controllers\Config\Add\Equips;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;

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

            'equipName' => 'required|min:2|max:200|unique:nagios_services,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'pinName.*' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/', // TODO : MAKE PIN NAME UNIQUE
            'hallName.*' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
            'workingState.*' => 'required',
            'inputNbr.*' => 'required|min:1|max:10',
            'site' => 'required',
         
        ],[
         
            'equipName.required' => 'the equipement name field is empty',
            'pinName.*.required' => 'the pin name field is empty',
            'hallName.*.required' => 'the hall name field is empty',
            'workingState.*.required' => 'the working state field is empty',
            'inputNbr.*.required' => 'the input number field is empty',
            'site.required' => 'choose the site that the equipement belong to',
        ]);

        $add_to_box = DB::table('nagios_hosts')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_hosts.host_object_id', $box_id)
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varname','BOXTYPE')
            ->select('nagios_hosts.display_name as box_name','nagios_customvariables.varvalue as box_type')
            ->first();

        $equipName = $request->equipName;
        $pinName = $request->pinName;
        $hallName = $request->hallName;
        $pinINnbr = $request->inputNbr;
        $workingState = $request->workingState;

        // Define pin
        for ($i=0; $i < sizeof($pinName); $i++) {

            $define_service = "define service {\n\tuse\t\t\tbox-service\n\thost_name\t\t".$add_to_box->box_name."\n\tservice_description\t".$pinName[$i]."\n\tcheck_command\t\t".$add_to_box->box_type."_IN".$pinINnbr[$i]."!".$workingState[$i]."\n}\n\n"; 

            $box_dir = "/usr/local/nagios/etc/objects/boxes/".$add_to_box->box_name."/".$pinName[$i].".cfg";

            file_put_contents($box_dir, $define_service);

            // Add equip path to nagios.cfg file
            $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/boxes/{$add_to_box->box_name}/{$pinName[$i]}.cfg";
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

            // Add Details To database
            $add_details = EquipsDetail::create([
                'site_name' => $request->site,
                'box_name' => $add_to_box->box_name,
                'box_type' => $add_to_box->box_type,
                'equip_name' => $equipName,
                'pin_name' => $pinName[$i],
                'working_state' => $workingState[$i],
                'hall_name' => $hallName[$i],
            ]);

        }

        $add_equip = EquipsNames::create([
            'site_name' => $request->site,
            'box_name' => $add_to_box->box_name,
            'equip_name' => $request->equipName
        ]);

        shell_exec('sudo service nagios restart');

        return redirect()->route('monitoring.equips');
        
    }
}
