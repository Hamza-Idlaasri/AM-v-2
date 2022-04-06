<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service extends Controller
{
    public function __construct()
    {
        $this->middleware(['agent']);
    }
    
    public function deleteService($service_id)
    {
        $service_deleted = DB::table('nagios_services')
            ->where('service_id',$service_id)
            ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
            ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name')
            ->get();

        $path = "/usr/local/nagios/etc/objects/hosts/".$service_deleted[0]->host_name."/".$service_deleted[0]->service_name.".cfg";

        if (is_file($path)) 
        {
            unlink($path);

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/hosts/{$service_deleted[0]->host_name}/{$service_deleted[0]->service_name}.cfg", '', $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);

        } else
            return 'WORNING: No service found';
        
        shell_exec('sudo service nagios restart');
        
        return redirect()->route('configServices');
        
    }
}
