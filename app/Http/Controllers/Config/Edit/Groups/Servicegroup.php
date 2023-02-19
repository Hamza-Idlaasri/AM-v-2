<?php

namespace App\Http\Controllers\Config\Edit\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Servicegroup extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function editSG(Request $request,$servicegroup_id)
    {
        // validation
        $this->validate($request,[
            'servicegroup_name' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
            'members' => 'required',
        ],[
            'members.required' => 'Please check services members for your servicegroup',
        ]);

        $members = [];

        foreach ($request->members as $member) {
            
            $element = DB::table('nagios_services')
            ->where('service_object_id', $member)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name')
            ->get();

            array_push($members, $element[0]->host_name);
            array_push($members, $element[0]->service_name);
        }

        $define_servicegroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t".$request->servicegroup_name."\n\talias\t\t\t\t".$request->servicegroup_name."\n\tmembers\t\t\t\t".implode(',',$members)."\n}\n";

        $path = "/usr/local/nagios/etc/objects/servicegroups";

        $old_servicegroup = DB::table('nagios_servicegroups')
            ->where('nagios_servicegroups.servicegroup_id', $servicegroup_id)
            ->select('nagios_servicegroups.alias as servicegroup_name')
            ->get();

        file_put_contents($path."/".$old_servicegroup[0]->servicegroup_name.'.cfg', $define_servicegroup);

        if ($old_servicegroup[0]->servicegroup_name != $request->servicegroup_name) {

            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=".$path."/".$old_servicegroup[0]->servicegroup_name.".cfg", "cfg_file=".$path."/".$request->servicegroup_name.".cfg", $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

            rename($path."/".$old_servicegroup[0]->servicegroup_name.'.cfg', $path."/".$request->servicegroup_name.'.cfg');
        }

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect('/config/servicegroups');
    }
}
