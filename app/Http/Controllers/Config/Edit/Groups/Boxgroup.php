<?php

namespace App\Http\Controllers\Config\Edit\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Boxgroup extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function editBG($boxgroup_id, Request $request)
    {
        // validation
        $this->validate($request,[
            'boxgroup_name' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
            'members' => 'required',
        ],[
            'members.required' => 'Please check boxes members for your boxgroup',
        ]);

        $members = [];

        foreach ($request->members as $member) {
           array_push($members,$member);
        }

        $define_boxgroup = "\ndefine hostgroup {\n\thostgroup_name\t\t".$request->boxgroup_name."\n\talias\t\t\t".$request->boxgroup_name."\n\tmembers\t\t\t".implode(',',$members)."\n}\n";

        $path = "/usr/local/nagios/etc/objects/boxgroups";

        $old_boxgroup = DB::table('nagios_hostgroups')
        ->where('hostgroup_id',$boxgroup_id)
        ->get();

        file_put_contents($path."/".$old_boxgroup[0]->alias.".cfg", $define_boxgroup);

        if($old_boxgroup[0]->alias != $request->boxgroup_name)
        {
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=".$path."/".$old_boxgroup[0]->alias.".cfg", "cfg_file=".$path."/".$request->boxgroup_name.".cfg", $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

            rename($path."/".$old_boxgroup[0]->alias.".cfg", $path."/".$request->boxgroup_name.".cfg");
        }

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect('/config/boxgroups');   
    }
}
