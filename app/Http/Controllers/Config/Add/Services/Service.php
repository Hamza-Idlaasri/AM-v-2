<?php

namespace App\Http\Controllers\Config\Add\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Service extends Controller
{
    public function createService(Request $request)
    {
        // validation
        $this->validate($request,[

            'service' => 'required',
            'host' => 'required',
            'community' => 'nullable|max:100',
            'portNbr' => 'nullable'
        ]);

        switch ($request->service) {
            
            // Windows
            case 'CPU Usage':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;
                
                file_put_contents($path."/CPU Usage.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tCPU Usage\n\tcheck_command\t\tcheck_ncpa!-t '' -P 5693 -M cpu/percent -w 20 -c 40 -q 'aggregate=avg'\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/CPU Usage.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;

            case 'RAM':
                
                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/RAM.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tRAM\n\tcheck_command\t\tcheck_ncpa!-t '' -P 5693 -M memory/virtual -w 50 -c 80 -u G\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/RAM.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;

            case 'Process Count':
                
                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;
                  
                file_put_contents($path."/Process Count.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tProcess Count\n\tcheck_command\t\tcheck_ncpa!-t '' -P 5693 -M processes -w 150 -c 200\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/Process Count.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;

            case 'Disk C':
                
                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Disk C.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->hostName."\n\tservice_description\tDisk C\n\tcheck_command\t\tcheck_ncpa!-t '' -P 5693 -M processes -w 150 -c 200\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->hostName}/Disk C.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;

            // Linux
            case 'PING(linux)':
                
                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/PING.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tPING\n\tcheck_command\t\tcheck_ping!100.0,20%!500.0,60%\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/PING.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Current Load':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Current Load.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tCurrent Load\n\tcheck_command\t\tcheck_local_load!5.0,4.0,3.0!10.0,6.0,4.0\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Current Load.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Total Processes':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Total Processes.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tTotal Processes\n\tcheck_command\t\tcheck_nrpe!check_total_procs\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Total Processes.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Current Users':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Current Users.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tCurrent Users\n\tcheck_command\t\tcheck_nrpe!check_users\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Current Users.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'SSH':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/SSH.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tSSH\n\tcheck_command\t\tcheck_nrpe!check_ssh\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/SSH.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'HTTP':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/HTTP.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tHTTP\n\tcheck_command\t\tcheck_http\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/HTTP.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Root Partition':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Root Partition.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tRoot Partition\n\tcheck_command\t\tcheck_local_disk!20%!10%!/\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Root Partition.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Swap Usage':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Swap Usage.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tSwap Usage\n\tcheck_command\t\tcheck_local_swap!20!10\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Swap Usage.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            // Router
            case 'PING(router)':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/PING.cfg","define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tPING\n\tcheck_command\t\tcheck_ping!200.0,20%!600.0,60%\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/PING.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Port n Link Status(router)':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Port ".$request->portNbr." Link Status.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tPort ".$request->portNbr." Link Status\n\tcheck_command\t\tcheck_snmp!-C ".$request->community." -o ifOperStatus.".$request->portNbr." -r 1 -m RFC1213-MIB\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Port ".$request->portNbr." Link Status.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Uptime(router)':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Uptime.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tUptime\n\tcheck_command\t\tcheck_snmp!-C ".$request->community." -o sysUpTime.0\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Uptime.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            // Switch
            case 'PING(switch)':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/PING.cfg","define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tPING\n\tcheck_command\t\tcheck_ping!200.0,20%!600.0,60%\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/PING.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Port n Link Status(switch)':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Port ".$request->portNbr." Link Status.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tPort ".$request->portNbr." Link Status\n\tcheck_command\t\tcheck_snmp!-C ".$request->community." -o ifOperStatus.".$request->portNbr." -r 1 -m RFC1213-MIB\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Port ".$request->portNbr." Link Status.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Port n Bandwidth Usage':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                foreach (DB::table('nagios_hosts')->where('display_name',$request->host)->select('address')->get() as $key) {
                    $addressIP = $key->address;
                }

                file_put_contents($path."/Port ".$request->portNbr." Bandwidth Usage.cfg","define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tPort ".$request->portNbr." Bandwidth Usage\n\tcheck_command\t\tcheck_local_mrtgtraf!/var/lib/mrtg/".$addressIP."_".$request->portNbr.".log!AVG!1000000,1000000!5000000,5000000!10\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Port ".$request->portNbr." Bandwidth Usage.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            case 'Uptime(switch)':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Uptime.cfg","define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tUptime\n\tcheck_command\t\tcheck_snmp!-C ".$request->community." -o sysUpTime.0\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Uptime.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
            
            // Printer
            case 'PING(printer)':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/PING.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tPING\n\tcheck_command\t\tcheck_ping!3000.0,80%!5000.0,100%\n\tnormal_check_interval\t\t5\nretry_check_interval\t\t1\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/PING.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;

            case 'Printer Status':

                $path = "/usr/local/nagios/etc/objects/hosts/".$request->host;

                file_put_contents($path."/Printer Status.cfg", "define service {\n\tuse\t\t\tgeneric-service\n\thost_name\t\t".$request->host."\n\tservice_description\tPrinter Status\n\tcheck_command\t\tcheck_hpjd!-C ".$request->community." \n\tnormal_check_interval\t5\n\tretry_check_interval\t1\n}\n\n");
                $cfg_file = "\ncfg_file=/usr/local/nagios/etc/objects/hosts/{$request->host}/Printer Status.cfg";
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $cfg_file, FILE_APPEND);

                break;
        }
        
        shell_exec('sudo service nagios restart');

        return redirect()->route('monitoring.services');
        // return back();
    }
}
