<?php

namespace App\Http\Controllers\Download\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Models\UsersSite;

class Services extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    public function pdf(Request $request)
    {
        if ($request->data == 'null') {

            return redirect()->back();

        } else {
          
            $services_history = json_decode($request->data);

            $pdf = PDF::loadView('download.services', compact('services_history'))->setPaper('a4', 'landscape');

            return $pdf->stream('services_historique'.date('Y-m-d H:i:s').'.pdf');
        }
    }

    // public function ServicesHistoric()
    // {
    //     $services_name = $this->getServicesName()->get();
        
    //     $services_histories = [];

    //     foreach ($services_name as $service) {

    //         $all_services_checks = $this->getServicesChecks()
    //             ->where('nagios_servicechecks.service_object_id','=',$service->service_object_id)
    //             ->get();

    //         if(sizeof($all_services_checks))
    //         {
    //             $status = $this->getStatus($all_services_checks);  

    //             for ($i=0; $i < sizeof($status); $i++) {
                    
    //                 $service_checks = $this->getServicesChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][0])
    //                     ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
    //                     ->get();
                    
    //                 $end_host_checks = $this->getServicesChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][1])
    //                     ->select('nagios_hosts.display_name as host_name','nagios_services.display_name as service_name','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
    //                     ->get();

    //                 $service_checks[0]->end_time = $end_host_checks[0]->end_time;

    //                 array_push($services_histories,$service_checks[0]);
    //             }

    //         } else {
                            
    //             continue;
    //         }

    //     }

    //     return $services_histories;
    // }

    // public function getServicesChecks()
    // {
    //     $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

    //     return DB::table('nagios_servicechecks')
    //         ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
    //         ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //         ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //         ->where('nagios_customvariables.varvalue',$site_name)
    //         ->where('alias','host')
    //         ->select('nagios_hosts.alias','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.*');
    // }

    // public function getServicesName()
    // {
    //     $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

    //     return DB::table('nagios_hosts')
    //         ->where('alias','host')
    //         ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //         ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //         ->where('nagios_customvariables.varvalue',$site_name)
    //         ->select('nagios_services.display_name as host_name','nagios_services.service_object_id','nagios_services.display_name as service_name');
    // }

    // public function getStatus($service)
    // {
    //     $status = [];

    //     $interval = [];

    //     for ($i=0; $i < sizeof($service); $i++) { 
                
    //         if($i == 0)
    //         {
    //             array_push($interval,$service[0]->servicecheck_id);
    //         }

    //         if ($i > 0 && $i < sizeof($service)-1) {
                
    //             if($service[$i]->state == $service[$i-1]->state)
    //             {
    //                 continue;

    //             } else {

    //                 array_push($interval,$service[$i-1]->servicecheck_id);

    //                 array_push($status,$interval);

    //                 $interval = [];

    //                 array_push($interval,$service[$i]->servicecheck_id);

    //             }

    //         }

    //         if($i == sizeof($service)-1)
    //         {
    //             if($service[$i]->state == $service[$i-1]->state)
    //             {
    //                 array_push($interval,$service[$i]->servicecheck_id);
    //                 array_push($status,$interval);

    //             } else {

    //                 array_push($interval,$service[$i-1]->servicecheck_id);
    //                 array_push($status,$interval);

    //                 $interval = [];

    //                 array_push($interval,$service[$i]->servicecheck_id);
    //                 array_push($interval,$service[$i]->servicecheck_id);
    //                 array_push($status,$interval);
    //             }
    //         }

    //     }

    //     return $status;
    // }
}
