<?php

namespace App\Http\Controllers\Config\Add\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Servicegroup extends Controller
{
    // Add servicegroup
    public function addSG()
    {
        $services = DB::table('nagios_hosts')
        ->where('alias','host')
        ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
        ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_services.service_object_id')
        ->get();

        return view('config.groups.add.addSG', compact('services'));
    }

    // Create New ServiceGroup
    public function createSG(Request $request)
    {
        // validation
        $this->validate($request,[
            'servicegroup_name' => 'required',
            'members' => 'required',
        ],[
            'members.required' => 'Please check hosts members for your servicegroup',
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

        $path = "/usr/local/nagios/etc/objects/servicegroups/".$request->servicegroup_name.".cfg";

        file_put_contents($path, $define_servicegroup);
        $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/servicegroups/".$request->servicegroup_name.".cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        shell_exec('sudo service nagios restart');

        return redirect('/config/servicegroups');
    }

}
