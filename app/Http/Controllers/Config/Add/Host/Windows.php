<?php

namespace App\Http\Controllers\Config\Add\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsersSite;

class Windows extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function windows(Request $request)
    {
        // validation
        $this->validate($request,[
            'hostName' => 'required|min:2|max:200|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'addressIP' => 'required|min:7|max:15',
            'site' => 'required',
        ],[
            'addressIP.required' => 'the IP address field is empty',
            'site.required' => 'choose the site that the Windows machine belong to',
        ]);

        $path = "/usr/local/nagios/etc/objects/hosts/{$request->site}/{$request->hostName}";

        if(!is_dir($path))
            mkdir($path);
        
        // Parent relationship
        if($request->input('hosts'))
            $define_host = "define host {\n\tuse\t\t\twindows-server\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\tcheck_command\t\t\tcheck_ncpa!-t '' -P 5693 -M system/agent_version\n\t_site\t\t\t".$request->site."\n\tparents\t\t\t".$request->input('hosts')."\n}\n\n";
        else
            $define_host = "define host {\n\tuse\t\t\twindows-server\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\tcheck_command\t\t\tcheck_ncpa!-t '' -P 5693 -M system/agent_version\n\t_site\t\t\t".$request->site."\n}\n\n";

        // Define Host :
        file_put_contents("{$path}/{$request->hostName}.cfg", $define_host);
        $cfg_file = "\n\ncfg_file={$path}/{$request->hostName}.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);


        // Define Services :
        
        file_put_contents("{$path}/CPU Usage.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tCPU Usage\n\tcheck_command\t\tcheck_ncpa!-t '' -P 5693 -M cpu/percent -w 60 -c 80 -q 'aggregate=avg'\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/CPU Usage.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
        
        file_put_contents("{$path}/RAM.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tRAM\n\tcheck_command\t\tcheck_ncpa!-t '' -P 5693 -M memory/virtual -w 50 -c 80 -u G\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/RAM.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
        
        file_put_contents("{$path}/Process Count.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tProcess Count\n\tcheck_command\t\tcheck_ncpa! -P 5693 -M 'disk/logical/C:|' --units G\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/Process Count.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
        
        file_put_contents("{$path}/Disk C.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tDisk C\n\tcheck_command\t\tcheck_ncpa!-t '' -P 5693 -M processes -w 150 -c 200\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/Disk C.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('monitoring.hosts');

    }
}
