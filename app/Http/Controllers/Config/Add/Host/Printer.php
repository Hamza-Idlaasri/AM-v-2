<?php

namespace App\Http\Controllers\Config\Add\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsersSite;

class Printer extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function printer(Request $request)
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        // validation
        $this->validate($request,[
            'hostName' => 'required|min:2|max:20|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'addressIP' => 'required|min:7|max:15',
            'community' => 'required|max:25'
        ],[
            'addressIP.required' => 'the IP address field is empty',
        ]);
        
        $path = "/usr/local/nagios/etc/objects/hosts/".$request->hostName;

        if(!is_dir($path))
            mkdir($path);

        // Parent relationship
        if($request->input('hosts'))
            $define_host = "define host {\n\tuse\t\t\tgeneric-printer\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\tparents\t\t\t".$request->input('hosts')."\n\t_site\t\t\t".$site_name."\n}\n\n";
        else
            $define_host = "define host {\n\tuse\t\t\tgeneric-printer\n\thost_name\t\t".$request->hostName."\n\talias\t\t\thost\n\taddress\t\t\t".$request->addressIP."\n\t_site\t\t\t".$site_name."\n}\n\n";
        
        // Host :
        file_put_contents($path."/".$request->hostName.".cfg", $define_host);
        $cfg_file = "\n\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/{$request->hostName}.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        // Service :
        file_put_contents($path."/PING.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tPING\n\tcheck_command\t\tcheck_ping!3000.0,80%!5000.0,100%\n\tnormal_check_interval\t\t5\nretry_check_interval\t\t1\n}\n\n");
        $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/PING.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

        file_put_contents($path."/Printer Status.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tPrinter Status\n\tcheck_command\t\tcheck_hpjd!-C ".$request->community."\n\tnormal_check_interval\t5\n\tretry_check_interval\t1\n}\n\n");
        $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/Printer Status.cfg";
        file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);
        
        shell_exec('sudo service nagios restart');

        return redirect()->route('monitoring.hosts');
        
    }
}
