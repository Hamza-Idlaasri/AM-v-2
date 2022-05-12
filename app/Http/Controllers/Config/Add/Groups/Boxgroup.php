<?php

namespace App\Http\Controllers\Config\Add\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Boxgroup extends Controller
{
    public function __construct()
    {
        $this->middleware(['admin','agent']);
    }

    public function addBG()
    {
        $boxes = DB::table('nagios_hosts')
            ->where('alias','box')
            ->select('nagios_hosts.display_name as box_name')
            ->get();
        
        return view('config.groups.add.addBG', compact('boxes'));
    }

    public function createBG(Request $request)
    {
        // validation
        $this->validate($request,[
            'boxgroup_name' => 'required|min:2|max:20|unique:nagios_hostgroups,alias|regex:/^[a-zA-Z0-9-_+ ]/',
            'members' => 'required',
        ],[
            'members.required' => 'Please check group members for your boxgroup',
        ]);

        $members = [];

        foreach ($request->members as $member) {
           array_push($members,$member);
        }

        $define_boxgroup = "\ndefine hostgroup {\n\thostgroup_name\t\t".$request->boxgroup_name."\n\talias\t\t\t".$request->boxgroup_name."\n\tmembers\t\t\t".implode(',',$members)."\n}\n";

        $path = "/usr/local/nagios/etc/objects/boxgroups/".$request->boxgroup_name.".cfg";

        file_put_contents($path, $define_boxgroup);
        $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/boxgroups/".$request->boxgroup_name.".cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        shell_exec('sudo service nagios restart');

        return redirect('/config/boxgroups');
    }
}
