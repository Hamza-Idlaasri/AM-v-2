<?php

namespace App\Http\Controllers\Download\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\BoxesExcel;
use Excel;
use App\Models\UsersSite;

class Boxes extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function csv()
    {
        date_default_timezone_set('Africa/Casablanca');

        return Excel::download(new BoxesExcel($this->BoxesHistoric()), 'boxes_historique '.date('Y-m-d H:i:s').'.csv');
    }

    public function BoxesHistoric()
    {
        $boxes_histories = [];
     
        $boxes_name = $this->getBoxesName()->get();

        foreach ($boxes_name as $box) {

            $all_boxes_checks = $this->getBoxesChecks()
                ->where('nagios_hostchecks.host_object_id','=',$box->host_object_id)
                ->get();

            if(sizeof($all_boxes_checks))
            {
                $status = $this->getStatus($all_boxes_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $box_checks = $this->getBoxesChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][0])
                        ->select('nagios_hosts.display_name','nagios_hosts.address','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                        ->get();
                    
                    $end_host_checks = $this->getBoxesChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][1])
                        ->select('nagios_hosts.display_name','nagios_hosts.address','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                        ->get();

                    $box_checks[0]->end_time = $end_host_checks[0]->end_time;

                    switch ($box_checks[0]->state) {
                
                        case 0:
                            $box_checks[0]->state = 'Up';
                            break;
                        case 1:
                            $box_checks[0]->state = 'Down';
                            break;
                        case 2:
                            $box_checks[0]->state = 'Unreachable';
                            break;
        
                    }

                    array_push($boxes_histories,$box_checks[0]);
                }

            } else {
                            
                continue;
            }

        }

        return $boxes_histories;
    }

    public function getBoxesChecks()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hostchecks')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('alias','box')
            ->where('is_raw_check','=', 0)
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.*','nagios_hosts.host_object_id','nagios_hostchecks.*');
    }

    public function getBoxesName()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
            ->where('alias','box')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
            ->orderBy('display_name');
    }

    public function getStatus($box)
    {
        $status = [];

        $interval = [];

        for ($i=0; $i < sizeof($box); $i++) { 
                
            if($i == 0)
            {
                array_push($interval,$box[0]->hostcheck_id);
            }

            if ($i > 0 && $i < sizeof($box)-1) {
                
                if($box[$i]->state == $box[$i-1]->state)
                {
                    continue;

                } else {

                    array_push($interval,$box[$i-1]->hostcheck_id);

                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$box[$i]->hostcheck_id);

                }

            }

            if($i == sizeof($box)-1)
            {
                if($box[$i]->state == $box[$i-1]->state)
                {
                    array_push($interval,$box[$i]->hostcheck_id);
                    array_push($status,$interval);

                } else {

                    array_push($interval,$box[$i-1]->hostcheck_id);
                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$box[$i]->hostcheck_id);
                    array_push($interval,$box[$i]->hostcheck_id);
                    array_push($status,$interval);
                }
            }

        }

        return $status;
    }

}
