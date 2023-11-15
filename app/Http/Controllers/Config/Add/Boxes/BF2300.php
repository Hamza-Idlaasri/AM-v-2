<?php

namespace App\Http\Controllers\Config\Add\Boxes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsersSite;

class BF2300 extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }

    public function createBox(Request $request)
    {
        $site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        // validation
        $this->validate($request, [

            'boxName' => 'required|min:2|max:200|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'addressIP' => 'required',
            'site' => 'required_unless:site,!=,specific'

        ], [
            'addressIP.required' => 'the IP address field is empty',
            'site.required_unless' => 'Please choose a site'
        ]);

        $box_dir = "/usr/local/nagios/etc/objects/boxes/" . $request->boxName;

        if (!is_dir($box_dir))
            mkdir($box_dir);

        // Parent relationship
        if ($request->input('hosts'))
            $define_host = "define host {\n\tuse\t\t\tbox-server\n\thost_name\t\t" . $request->boxName . "\n\talias\t\t\tbox\n\taddress\t\t\t" . $request->addressIP . "\n\t_site\t\t\t" . $request->site == 'specific' ? $site_name : '' . "\n\t_boxType\t\t\tbf2300\n\tparents\t\t\t" . $request->input('hosts') . "\n}\n\n";
        else
            $define_host = "define host {\n\tuse\t\t\tbox-server\n\thost_name\t\t" . $request->boxName . "\n\talias\t\t\tbox\n\taddress\t\t\t" . $request->addressIP . "\n\t_site\t\t\t" . $request->site == 'specific' ? $site_name : '' . "\n\t_boxType\t\t\tbf2300\n}\n\n";

        file_put_contents($box_dir . "/" . $request->boxName . ".cfg", $define_host);

        // Add box path to nagios.cfg file
        $cfg_file = "\n\ncfg_file=/usr/local/nagios/etc/objects/boxes/{$request->boxName}/{$request->boxName}.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        // Restart nagios
        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('monitoring.boxes');
    }
}
