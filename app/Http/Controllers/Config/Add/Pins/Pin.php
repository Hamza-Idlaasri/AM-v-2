<?php

namespace App\Http\Controllers\Config\Add\Pins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;

class Pin extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function createPin(Request $request,$box_id)
    {
        // validation
        $this->validate($request,[

            'equipName' => 'required',
            'pinName.*' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/', // TODO : MAKE PIN NAME UNIQUE
            'hallName.*' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
            'workingState.*' => 'required',
            'inputNbr.*' => 'required|min:1|max:12',
         
        ],[
         
            'equipName.required' => 'Please Choose an equipment',
            'pinName.*.required' => 'the pin name field is empty',
            'hallName.*.required' => 'the hall name field is empty',
            'workingState.*.required' => 'the working state field is empty',
            'inputNbr.*.required' => 'the input number field is empty',
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
                'site_name' => $this->getSiteName($box_id),
                'box_name' => $add_to_box->box_name,
                'box_type' => $add_to_box->box_type,
                'equip_name' => $equipName,
                'pin_name' => $pinName[$i],
                'input_nbr' => $inputNbr[$i],
                'working_state' => $workingState[$i],
                'hall_name' => $hallName[$i],
            ]);

        }

        // TODO : POSSIBLE TO ADD NEW PIN UNDER CONDITION IF EQUIP NAME NOT EXIST
        $add_equip = EquipsNames::create([
            'site_name' => $this->getSiteName($box_id),
            'box_name' => $add_to_box->box_name,
            'equip_name' => $request->equipName
        ]);

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('monitoring.equips');
        
    }

    public function getSiteName($box_id) {

        return DB::table('nagios_hosts')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_hosts.host_object_id', $box_id)
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varname','SITE')
            ->select('nagios_customvariables.varvalue as site_name')
            ->first();
    }
}
