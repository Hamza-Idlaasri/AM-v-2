<?php

namespace App\Http\Controllers\Config\Edit\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Hostgroup extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function editHG($hostgroup_id, Request $request)
    {
        // validation
        $this->validate($request,[
            'hostgroup_name' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
            'members' => 'required',
        ],[
            'members.required' => 'Please check hosts members for your hostgroup',
        ]);

        $members = [];

        foreach ($request->members as $member) {
           array_push($members,$member);
        }

        $define_hostgroup = "\ndefine hostgroup {\n\thostgroup_name\t\t".$request->hostgroup_name."\n\talias\t\t\t".$request->hostgroup_name."\n\tmembers\t\t\t".implode(',',$members)."\n}\n";

        $path = "/usr/local/nagios/etc/objects/hostgroups";

        $old_hostgroup = DB::table('nagios_hostgroups')
        ->where('hostgroup_id',$hostgroup_id)
        ->get();

        file_put_contents($path."/".$old_hostgroup[0]->alias.".cfg", $define_hostgroup);

        if($old_hostgroup[0]->alias != $request->hostgroup_name)
        {
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=".$path."/".$old_hostgroup[0]->alias.".cfg", "cfg_file=".$path."/".$request->hostgroup_name.".cfg", $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

            rename($path."/".$old_hostgroup[0]->alias.".cfg", $path."/".$request->hostgroup_name.".cfg");
        }

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect('/config/hostgroups');   
    }
}
