<?php

namespace App\Http\Controllers\Config\Delete\Groups;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Servicegroup extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function deleteSG($servicegroup_id)
    {
        $SG_deleted = DB::table('nagios_servicegroups')
        ->where('servicegroup_id', $servicegroup_id)
        ->get();

        $path = "/usr/local/nagios/etc/objects/servicegroups/".$SG_deleted[0]->alias.".cfg";

        unlink($path);

        $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
        $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/servicegroups/".$SG_deleted[0]->alias.".cfg", '', $nagios_file_content);
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

        shell_exec('sudo service nagios restart');

        return back();
    }
}
