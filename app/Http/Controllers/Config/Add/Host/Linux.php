<?php

namespace App\Http\Controllers\Config\Add\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsersSite;

class Linux extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function linux(Request $request)
    {
        // validation
        $this->validate($request,[
            'hostName' => 'required|min:2|max:200|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'addressIP' => 'required|min:7|max:15',
            'site' => 'required',
        ],[
            'addressIP.required' => 'the IP address field is empty',
            'site.required' => 'choose the site that the Linux machine belong to',
        ]);

        $path = "/usr/local/nagios/etc/objects/hosts/{$request->site}/{$request->hostName}";

        if(!is_dir($path))
            mkdir($path);

        // Parent relationship
        if($request->input('hosts'))
            $define_host = "define host {\n\tuse\t\t\tlinux-server\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\t_site\t\t\t".$request->site."\n\tparents\t\t\t".$request->input('hosts')."\n}\n\n";
        else
            $define_host = "define host {\n\tuse\t\t\tlinux-server\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\t_site\t\t\t".$request->site."\n}\n\n";

        // Hosts : 
        file_put_contents("{$path}/{$request->hostName}.cfg", $define_host);
        $cfg_file = "\n\ncfg_file={$path}/{$request->hostName}.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        // Services :
        file_put_contents("{$path}/PING.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tPING\n\tcheck_command\t\tcheck_ping!100.0,20%!500.0,60%\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/PING.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        file_put_contents("{$path}/Current Load.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tCurrent Load\n\tcheck_command\t\tcheck_local_load!5.0,4.0,3.0!10.0,6.0,4.0\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/Current Load.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        file_put_contents("{$path}/Total Processes.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tTotal Processes\n\tcheck_command\t\tcheck_nrpe!check_total_procs\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/Total Processes.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        file_put_contents("{$path}/Current Users.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tCurrent Users\n\tcheck_command\t\tcheck_nrpe!check_users\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/Current Users.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        file_put_contents("{$path}/SSH.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tSSH\n\tcheck_command\t\tcheck_nrpe!check_ssh\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/SSH.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        file_put_contents("{$path}/HTTP.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tHTTP\n\tcheck_command\t\tcheck_http\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/HTTP.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        file_put_contents("{$path}/Root Partition.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tRoot Partition\n\tcheck_command\t\tcheck_local_disk!20%!10%!/\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/Root Partition.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        file_put_contents("{$path}/Swap Usage.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tSwap Usage\n\tcheck_command\t\tcheck_local_swap!20!10\n}\n\n");
        $cfg_file = "\ncfg_file={$path}/Swap Usage.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
        
        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('monitoring.hosts');
        
    }
}
