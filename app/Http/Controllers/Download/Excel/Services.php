<?php

namespace App\Http\Controllers\Download\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ServicesExcel;
use Excel;

class Services extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    public function csv()
    {
        date_default_timezone_set('Africa/Casablanca');

        return Excel::download(new ServicesExcel($this->ServicesHistoric()), 'services_historique '.date('Y-m-d H:i:s').'.csv');
    }

    public function ServicesHistoric()
    {
        $services_name = $this->getServicesName()->get();
        
        $services_histories = [];

        foreach ($services_name as $service) {

            $all_services_checks = $this->getServicesChecks()
                ->where('nagios_servicechecks.service_object_id','=',$service->service_object_id)
                ->get();

            if(sizeof($all_services_checks))
            {
                $status = $this->getStatus($all_services_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $service_checks = $this->getServicesChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][0])
                        ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                        ->get();
                    
                    $end_host_checks = $this->getServicesChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][1])
                        ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                        ->get();

                    $service_checks[0]->end_time = $end_host_checks[0]->end_time;

                    switch ($service_checks[0]->state) {
                
                        case 0:
                            $service_checks[0]->state = 'Ok';
                            break;
                        case 1:
                            $service_checks[0]->state = 'Warning';
                            break;
                        case 2:
                            $service_checks[0]->state = 'Critical';
                            break;
                        case 3:
                            $service_checks[0]->state = 'Unknown';
                            break;
        
                    }

                    array_push($services_histories,$service_checks[0]);
                }

            } else {
                            
                continue;
            }

        }

        return $services_histories;
    }

    public function getServicesChecks()
    {
        return DB::table('nagios_servicechecks')
        ->select('nagios_hosts.alias','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.*')
        ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
        ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
        ->where('alias','host');
    }

    public function getServicesName()
    {
        return DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_services.display_name as host_name','nagios_services.service_object_id','nagios_services.display_name as service_name');
    }

    public function getStatus($service)
    {
        $status = [];

        $interval = [];

        for ($i=0; $i < sizeof($service); $i++) { 
                
            if($i == 0)
            {
                array_push($interval,$service[0]->servicecheck_id);
            }

            if ($i > 0 && $i < sizeof($service)-1) {
                
                if($service[$i]->state == $service[$i-1]->state)
                {
                    continue;

                } else {

                    array_push($interval,$service[$i-1]->servicecheck_id);

                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$service[$i]->servicecheck_id);

                }

            }

            if($i == sizeof($service)-1)
            {
                if($service[$i]->state == $service[$i-1]->state)
                {
                    array_push($interval,$service[$i]->servicecheck_id);
                    array_push($status,$interval);

                } else {

                    array_push($interval,$service[$i-1]->servicecheck_id);
                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$service[$i]->servicecheck_id);
                    array_push($interval,$service[$i]->servicecheck_id);
                    array_push($status,$interval);
                }
            }

        }

        return $status;
    }
}
