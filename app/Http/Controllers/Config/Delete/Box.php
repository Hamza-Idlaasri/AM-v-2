<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Box extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function deleteBox($box_id)
    {
        $box_deleted = DB::table('nagios_hosts')
            ->where('host_object_id', $box_id)
            ->select('nagios_hosts.display_name')
            ->get();
        
        $box_equips = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $box_id)
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name')
            ->get();

        $path = "/usr/local/nagios/etc/objects/boxes/".$box_deleted[0]->display_name;

        if(is_dir($path))
        {
            $objects = scandir($path);

            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    unlink($path. DIRECTORY_SEPARATOR .$object); 
                }
            }

            rmdir($path);

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/boxes/".$box_deleted[0]->display_name."/".$box_deleted[0]->display_name.".cfg", '', $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

            // Remove box equips
            foreach ($box_equips as $equip) {
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/boxes/".$equip->box_name."/".$equip->equip_name.".cfg", '', $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
            }

        } else {
            return 'WORNING: No box found';
        }

        shell_exec('sudo service nagios restart');

        return redirect()->route('config-boxes');

    }
}
