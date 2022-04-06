<?php

namespace App\Http\Controllers\Download\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\EquipsExcel;
use Excel;

class Equips extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function csv()
    {
        date_default_timezone_set('Africa/Casablanca');

        return Excel::download(new EquipsExcel($this->EquipsHistoric()), 'equips_historique '.date('Y-m-d H:i:s').'.csv');
    }

    public function EquipsHistoric()
    {
        $equips_name = $this->getEquipsName()->get();
        
        $equips_histories = [];

        foreach ($equips_name as $equip) {

            $all_equips_checks = $this->getEquipsChecks()
                ->where('nagios_servicechecks.service_object_id','=',$equip->service_object_id)
                ->get();

            if(sizeof($all_equips_checks))
            {
                $status = $this->getStatus($all_equips_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $equip_checks = $this->getEquipsChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][0])
                        ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                        ->get();
                    
                    $end_host_checks = $this->getEquipsChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][1])
                        ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                        ->get();

                    $equip_checks[0]->end_time = $end_host_checks[0]->end_time;

                    switch ($equip_checks[0]->state) {
                
                        case 0:
                            $equip_checks[0]->state = 'Ok';
                            break;
                        case 1:
                            $equip_checks[0]->state = 'Warning';
                            break;
                        case 2:
                            $equip_checks[0]->state = 'Critical';
                            break;
                        case 3:
                            $equip_checks[0]->state = 'Unknown';
                            break;
        
                    }

                    array_push($equips_histories,$equip_checks[0]);
                }

            } else {
                            
                continue;
            }

        }

        return $equips_histories;
    }

    public function getEquipsChecks()
    {
        return DB::table('nagios_servicechecks')
        ->select('nagios_hosts.alias','nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicechecks.*')
        ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
        ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
        ->where('alias','box');
    }

    public function getEquipsName()
    {
        return DB::table('nagios_hosts')
            ->where('alias','box')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_services.display_name as box_name','nagios_services.service_object_id','nagios_services.display_name as equip_name');
    }

    public function getStatus($equip)
    {
        $status = [];

        $interval = [];

        for ($i=0; $i < sizeof($equip); $i++) { 
                
            if($i == 0)
            {
                array_push($interval,$equip[0]->servicecheck_id);
            }

            if ($i > 0 && $i < sizeof($equip)-1) {
                
                if($equip[$i]->state == $equip[$i-1]->state)
                {
                    continue;

                } else {

                    array_push($interval,$equip[$i-1]->servicecheck_id);

                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$equip[$i]->servicecheck_id);

                }

            }

            if($i == sizeof($equip)-1)
            {
                if($equip[$i]->state == $equip[$i-1]->state)
                {
                    array_push($interval,$equip[$i]->servicecheck_id);
                    array_push($status,$interval);

                } else {

                    array_push($interval,$equip[$i-1]->servicecheck_id);
                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$equip[$i]->servicecheck_id);
                    array_push($interval,$equip[$i]->servicecheck_id);
                    array_push($status,$interval);
                }
            }

        }

        return $status;
    }
}
