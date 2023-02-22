<?php

namespace App\Http\Controllers\Config\Add\Boxes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsersSite;
use App\Models\EquipsDetail;

class BF1010 extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function createBox(Request $request)
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        // $equipNames = $request->input('equipName');
        // $equiINnbr = $request->input('inputNbr');
        
        // validation
        $this->validate($request,[

            'boxName' => 'required|min:2|max:200|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'addressIP' => 'required',
            // 'equipName.*' => 'required|min:2|max:20|unique:nagios_services,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            // 'inputNbr.*' => 'required',
            
        ],[
            'addressIP.required' => 'the IP address field is empty',
            // 'equipName.*.required' => 'the equipement name field is empty',
            // 'inputNbr.*.required' => 'the input number field is empty',
        ]);

        $box_dir = "/usr/local/nagios/etc/objects/boxes/".$request->boxName;

        if(!is_dir($box_dir))
            mkdir($box_dir);
            
        // Parent relationship
        if($request->input('hosts'))
            $define_host = "define host {\n\tuse\t\t\tbox-server\n\thost_name\t\t".$request->boxName."\n\talias\t\t\tbox\n\taddress\t\t\t".$request->addressIP."\n\tparents\t\t\t".$request->input('hosts')."\n\t_site\t\t\t".$site_name."\n\t_boxType\t\t\tbf1010\n}\n\n";
        else
            $define_host = "define host {\n\tuse\t\t\tbox-server\n\thost_name\t\t".$request->boxName."\n\talias\t\t\tbox\n\taddress\t\t\t".$request->addressIP."\n\t_site\t\t\t".$site_name."\n\t_boxType\t\t\tbf1010\n}\n\n";

        file_put_contents($box_dir."/".$request->boxName.".cfg", $define_host);

        // Add box path to nagios.cfg file
        $cfg_file = "\n\ncfg_file=/usr/local/nagios/etc/objects/boxes/{$request->boxName}/{$request->boxName}.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        // Define services
        // for ($i=0; $i < sizeof($equipNames); $i++) {

        //     $define_service = "define service {\n\tuse\t\t\tbox-service\n\thost_name\t\t".$request->boxName."\n\tservice_description\t".$equipNames[$i]."\n\tcheck_command\t\tbf1010_IN".$equiINnbr[$i]."\n}\n\n"; 
 
        //     $equip_file = fopen($box_dir."/".$equipNames[$i].".cfg", "w");
 
        //     fwrite($equip_file, $define_service);
            
        //     fclose($equip_file);

        //     // Add equip path to nagios.cfg file
        //     $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/boxes/{$request->boxName}/{$equipNames[$i]}.cfg";
        //     file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        // }

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('monitoring.boxes');
    }

}
