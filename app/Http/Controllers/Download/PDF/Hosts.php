<?php

namespace App\Http\Controllers\Download\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Models\UsersSite;

class Hosts extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    public function pdf()
    {
        $hosts_history = $this->HostsHistoric();

        $pdf = PDF::loadView('download.hosts', compact('hosts_history'))->setPaper('a4', 'landscape');

        date_default_timezone_set('Africa/Casablanca');

        return $pdf->stream('hosts_historique'.date('Y-m-d H:i:s').'.pdf');
    }

    public function HostsHistoric()
    {
        $hosts_histories = [];

        $hosts_name = $this->getHostsName()->get();
        
        foreach ($hosts_name as $host) {

            $all_hosts_checks = $this->getHostsChecks()
                ->where('nagios_hostchecks.host_object_id','=',$host->host_object_id)
                ->get();

            if(sizeof($all_hosts_checks))
            {
                $status = $this->getStatus($all_hosts_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $host_checks = $this->getHostsChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][0])
                        ->select('nagios_hosts.display_name','nagios_hosts.address','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                        ->get();
                    
                    $end_host_checks = $this->getHostsChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][1])
                        ->select('nagios_hosts.display_name','nagios_hosts.address','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                        ->get();

                    $host_checks[0]->end_time = $end_host_checks[0]->end_time;

                    switch ($host_checks[0]->state) {
                
                        case 0:
                            $host_checks[0]->state = 'Up';
                            break;
                        case 1:
                            $host_checks[0]->state = 'Down';
                            break;
                        case 2:
                            $host_checks[0]->state = 'Unreachable';
                            break;
        
                    }

                    array_push($hosts_histories,$host_checks[0]);
                }

            } else {

                continue;
            }

        }

        return $hosts_histories;
    }

    public function getHostsChecks()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hostchecks')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('alias','host')
            ->where('is_raw_check','=', 0)
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.*','nagios_hosts.host_object_id','nagios_hostchecks.*');
    }

    public function getHostsName()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
            ->orderBy('display_name');
    }

    public function getStatus($host)
    {
        $status = [];

        $interval = [];

        for ($i=0; $i < sizeof($host); $i++) { 
                
            if($i == 0)
            {
                array_push($interval,$host[0]->hostcheck_id);
            }

            if ($i > 0 && $i < sizeof($host)-1) {
                
                if($host[$i]->state == $host[$i-1]->state)
                {
                    continue;

                } else {

                    array_push($interval,$host[$i-1]->hostcheck_id);

                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$host[$i]->hostcheck_id);

                }

            }

            if($i == sizeof($host)-1)
            {
                if($host[$i]->state == $host[$i-1]->state)
                {
                    array_push($interval,$host[$i]->hostcheck_id);
                    array_push($status,$interval);

                } else {

                    array_push($interval,$host[$i-1]->hostcheck_id);
                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$host[$i]->hostcheck_id);
                    array_push($interval,$host[$i]->hostcheck_id);
                    array_push($status,$interval);
                }
            }

        }

        return $status;
    }
}
