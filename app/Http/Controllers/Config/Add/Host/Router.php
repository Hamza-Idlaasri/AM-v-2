<?php

namespace App\Http\Controllers\Config\Add\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsersSite;

class Router extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function router(Request $request)
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        // validation
        $this->validate($request,[
            'hostName' => 'required|min:2|max:200|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'addressIP' => 'required|min:7|max:15',
            'community' => 'required|max:25',
            'portsNbr' => 'required'
        ],[
            'addressIP.required' => 'the IP address field is empty',
        ]);

        $path = "/usr/local/nagios/etc/objects/hosts/".$request->hostName;

        if(!is_dir($path))
            mkdir($path);

        // Parent relationship
        if($request->input('hosts'))
            $define_host = "define host {\n\tuse\t\t\tgeneric-switch\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\tparents\t\t\t".$request->input('hosts')."\n\t_site\t\t\t".$site_name."\n}\n\n";
        else
            $define_host = "define host {\n\tuse\t\t\tgeneric-switch\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\t_site\t\t\t".$site_name."\n}\n\n";
        
        // Host :
        file_put_contents($path."/".$request->hostName.".cfg", $define_host);
        $cfg_file = "\n\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/{$request->hostName}.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        // Services :
        file_put_contents($path."/PING.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tPING\n\tcheck_command\t\tcheck_ping!200.0,20%!600.0,60%\n\tnormal_check_interval\t5\n\tretry_check_interval\t1\n}\n\n");
        $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/PING.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
        
        for ($i= 1; $i <= $request->portsNbr; $i++) { 
            
            file_put_contents($path."/Port ".$i." Link Status.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tPort ".$i." Link Status\n\tcheck_command\t\tcheck_snmp!-C ".$request->community." -o ifOperStatus.".$i." -r ".$i." -m RFC1213-MIB\n}\n\n");
            $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/Port ".$i." Link Status.cfg";
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
        }
        
        file_put_contents($path."/Uptime.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tUptime\n\tcheck_command\t\tcheck_snmp!-C ".$request->community." -o sysUpTime.0\n}\n\n");
        $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/Uptime.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
        
        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('monitoring.hosts');

    }
}
