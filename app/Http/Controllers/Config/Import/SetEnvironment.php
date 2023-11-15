<?php

namespace App\Http\Controllers\Config\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipsNames;
use App\Models\EquipsDetail;
use App\Models\UsersSite;

class SetEnvironment extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }

    public function setEnvironment(Request $request)
    {

        $rules = [];

        // foreach ($request->input('ip_address') as $boxName => $ipAddress) {
        //     $rules["ip_address.{$boxName}.*"] = 'required|ip|unique:nagios_hosts,address';
        // }

        // TODO: VALIDATE DATA
        $rules = [];

        foreach ($request->input('box_name') as $index => $boxName) {
            $rules["box_name.{$index}"] = 'required|min:2|max:200|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/';

            // Add rules for other fields
            $rules["ip_address.{$boxName}"] = 'required|ip|unique:nagios_hosts,address';
            // $rules["box_type.{$boxName}"] = 'required|in:BF1010,BF2300'; // Add more options if needed

            foreach ($request->input("equip_name.{$boxName}") as $equipIndex => $equipName) {
                $rules["equip_name.{$boxName}.{$equipIndex}"] = 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/';

                foreach ($request->input("pin_desc.{$boxName}.{$equipName}") as $pinIndex => $pinDesc) {
                    $rules["pin_desc.{$boxName}.{$equipName}.{$pinIndex}"] = 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/';
                    $rules["input_nbr.{$boxName}.{$equipName}.{$pinIndex}"] = 'required|numeric|min:1|max:12';
                    $rules["hall.{$boxName}.{$equipName}.{$pinIndex}"] = 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/';
                }
            }
        }

        $this->validate($request, $rules);

        // $this->validate($request, [

        //     'box_name.*' => 'required|min:2|max:200|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
        //     'ip_address.*.*' => 'required|ip|unique:nagios_hosts,address',
        //     'equip_name.*.*' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
        //     'pin_desc.*.*.*' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
        //     'input_nbr.*.*.*' => 'required|min:1|max:12',
        //     'hall.*.*.*' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
        //     // 'site' => 'required_unless:site,!=,specific'

        // ], [
        //     'box_name.*.required' => 'The :attribute field is required',
        //     'box_name.*.min' => 'The :attribute must be at least :min characters',
        //     'box_name.*.max' => 'The :attribute must not be greater than :max characters',
        //     'box_name.*.unique' => 'The :attribute is already taken',
        //     'box_name.*.regex' => 'The :attribute format is invalid',

        //     'ip_address.*.*.required' => 'The :attribute field is required',
        //     'ip_address.*.*.ip' => 'Please enter a valid :attribute',
        //     'ip_address.*.*.unique' => 'The :attribute is already taken',

        //     'equip_name.*.*.required' => 'The :attribute field is required',
        //     // 'site.required_unless' => 'Please choose a site'
        // ], [
        //     'box_name.*' => 'Box Name',
        //     'ip_address.*.*' => 'IP Address',
        //     'equip_name.*.*' => 'Equipment Name',
        //     'pin_desc.*.*.*' => 'Pin Description',
        //     'input_nbr.*.*.*' => 'Input Number',
        //     'hall.*.*.*' => 'Hall',
        // ]);

        $envir = $this->organizeData($request->all());

        dd($request->all());
        
        $site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        foreach ($envir as $box) {

            // Create box folder
            $this->create_box($box->box_name, $box->box_type, $box->ip_address, $site_name);

            // Add equips to tables and create pin files
            foreach ($box->equips as $equip) {

                // Add equip to Equips Names Table;
                $this->add_equip_to_equips_names_table($box->box_name, $equip->equip_name, $site_name);

                // Create Pins files and Add equip details to EuipsDetails Table
                foreach ($equip->pins as $pin) {
                    // Create Pin file
                    $this->create_pin_files($box->box_name, $box->box_type, $pin->pin_desc, $pin->input_nbr);

                    // Add equip details
                    $this->add_equip_details_to_equipsdetails_table($box->box_name, $box->box_type, $equip->equip_name, $pin->pin_desc, $pin->input_nbr, $pin->hall, $site_name);
                }
            }
        }

        $this->restart_nagios();

        return redirect()->route('monitoring.equips');
    }

    public function organizeData($envir)
    {

        // dd($envir);
        $box_name = $envir['box_name'];

        $boxes = [];

        for ($i = 0; $i < sizeof($box_name); $i++) {

            // IP Address
            $ip_address = $envir['ip_address'][$box_name[$i]];

            // Box Type
            $box_type = $envir['box_type'][$box_name[$i]];

            // Equips
            $equip_name = $envir['equip_name'][$box_name[$i]];

            // Pins
            $pin_desc = $envir['pin_desc'][$box_name[$i]];

            // Input Nbr
            $input_nbr = $envir['input_nbr'][$box_name[$i]];

            // Hall
            $hall = $envir['hall'][$box_name[$i]];

            $equips = [];

            foreach ($equip_name as $equip) {

                $pins = [];

                for ($j = 0; $j < sizeof($pin_desc[$equip]); $j++) {
                    $pins[] = (object)['pin_desc' => $pin_desc[$equip][$j], 'input_nbr' => $input_nbr[$equip][$j], 'hall' => $hall[$equip][$j]];
                }

                $equips[] = (object)['equip_name' => $equip, 'pins' => $pins];
            }

            // Boxes
            $boxes[] = (object) ['box_name' => $box_name[$i], 'ip_address' => $ip_address, 'box_type' => $box_type, 'equips' => $equips];
        }

        return $boxes;
    }

    public function create_box($box_name, $box_type, $ip_address, $site_name)
    {

        $box_type = $box_type == 'BF1010' ? 'bf1010' : 'bf2300';

        $box_dir = "/usr/local/nagios/etc/objects/boxes/" . $box_name;

        if (!is_dir($box_dir))
            mkdir($box_dir);

        // Parent relationship
        $define_host = "define host {\n\tuse\t\t\tbox-server\n\thost_name\t\t" . $box_name . "\n\talias\t\t\tbox\n\taddress\t\t\t" . $ip_address . "\n\t_site\t\t\t" . $site_name . "\n\t_boxType\t\t\t" . $box_type . "\n}\n\n";

        file_put_contents($box_dir . "/" . $box_name . ".cfg", $define_host);

        // Add box path to nagios.cfg file
        $cfg_file = "\n\ncfg_file=/usr/local/nagios/etc/objects/boxes/{$box_name}/{$box_name}.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
    }

    public function create_pin_files($box_name, $box_type, $pin_name, $input_nbr)
    {

        $define_service = "define service {\n\tuse\t\t\tbox-service\n\thost_name\t\t{$box_name}\n\tservice_description\t{$pin_name}\n\tcheck_command\t\t{$box_type}_IN{$input_nbr}!H\n}\n\n";

        $file_path = "/usr/local/nagios/etc/objects/boxes/{$box_name}/{$pin_name}.cfg";

        file_put_contents($file_path, $define_service);

        // Add equip path to nagios.cfg file
        $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/boxes/{$box_name}/{$pin_name}.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
    }

    public function add_equip_to_equips_names_table($box_name, $equip_name, $site_name)
    {
        EquipsNames::create([
            'site_name' => $site_name,
            'box_name' => $box_name,
            'equip_name' => $equip_name
        ]);
    }

    public function add_equip_details_to_equipsdetails_table($box_name, $box_type, $equip_name, $pin_name, $input_nbr, $hall, $site_name)
    {
        EquipsDetail::create([
            'site_name' => $site_name,
            'box_name' => $box_name,
            'box_type' => $box_type,
            'equip_name' => $equip_name,
            'pin_name' => $pin_name,
            'input_nbr' => $input_nbr,
            'working_state' => "H",
            'hall_name' => $hall,
        ]);
    }

    public function restart_nagios()
    {
        // Restart nagios
        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');
    }
}
