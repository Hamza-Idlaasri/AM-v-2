<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Services extends Component
{
    public function render()
    {
        $services_name = $this->getServicesName()->get();
        
        $services_status = $this->getServicesStatus($services_name);
        
        $datasets = $this->getChartRange();

        return view('livewire.statistic.services')
            ->with(['services_status' => $services_status, 'datasets' => $datasets])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getServicesChecks()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $date = date('Y-m-d H:i:s', strtotime('-24 hours', time()));

        if ($site_name == 'All') {
            
            return DB::table('nagios_servicechecks')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->where('alias','host')
                ->select('nagios_hosts.alias','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.*')
                ->orderBy('start_time')
                ->where('nagios_servicechecks.end_time','>=',$date);
        }
        else
        {
            return DB::table('nagios_servicechecks')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('alias','host')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.alias','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.*')
            ->orderBy('start_time')
            ->where('nagios_servicechecks.end_time','>=',$date);
        }
       
    }

    public function getServicesName()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == 'All') {
            
            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_services.display_name as host_name','nagios_services.service_object_id','nagios_services.display_name as service_name')
                ->orderBy('nagios_services.display_name');
        }
        else
        {
            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_services.display_name as host_name','nagios_services.service_object_id','nagios_services.display_name as service_name')
                ->orderBy('nagios_services.display_name');
        }
    }

    

    public function getServicesStatus($services_name)
    {
        $services_ok = 0;
        $services_warning = 0;
        $services_critical = 0;
        $services_unknown = 0;

        $services_checks = [];
        
        foreach ($services_name as $service) {

            $all_services_checks = $this->getServicesChecks()
                ->where('nagios_servicechecks.service_object_id','=',$service->service_object_id)
                ->get();

            if(sizeof($all_services_checks))
            {
                $status = $this->getInterval($all_services_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $service = $this->getServicesChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][0])->get();
                    array_push($services_checks,$service[0]);
                
                }

            } else {
                continue;
            }

        }

        foreach ($services_checks as $service) {
            

            switch ($service->state) {
                
                case 0:
                    $services_ok++;
                    break;
                
                case 1:
                    $services_warning++;
                    break;
                
                case 2:
                    $services_critical++;
                    break;
                
                case 3:
                    $services_unknown++;
                    break;
            }
        }

        return (object)['services_ok' => $services_ok,'services_warning' => $services_warning,'services_critical' => $services_critical,'services_unknown' => $services_unknown];
    }

    public function getInterval($service)
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

    public function getChartRange()
    {
        $datasets = [];

        $services = $this->getServicesName()->get();

        $data = [
            'equip_name' => '',
            'Ok' => '',
            'Warning' => '',
            'Critical' => '',
            'Unknown' => '',
        ];

        foreach ($services as $service) {

            // Get All host checks
            $service_checks = $this->getServicesChecks()->where('nagios_services.display_name', $service->service_name)->get();

            // Get Ranges
            $range = [];
            $service_ranges = [];

            if (sizeof($service_checks)) {
            
                for ($i=0; $i < sizeof($service_checks); $i++) {
                    
                    if ($i == 0) {
                        array_push($range, $service_checks[0]);
                    }

                    if ($i > 0 && $i < sizeof($service_checks)-1) {

                        if ($service_checks[$i]->state == $service_checks[$i-1]->state) {
                            continue;
                        } 
                        else
                        {
                            array_push($range,$service_checks[$i-1]);
                            array_push($service_ranges,$range);
                            $range = [];
                            array_push($range,$service_checks[$i]);
                        }

                    }

                    if ($i == sizeof($service_checks)-1) {
                        
                        if ($service_checks[$i]->state == $service_checks[$i-1]->state) {
                            array_push($range,$service_checks[$i]);
                            array_push($service_ranges,$range);
                            $range = [];
                        }
                        else
                        {
                            array_push($range,$service_checks[$i-1]);
                            array_push($service_ranges,$range);
                            $range = [];
                            array_push($range,$service_checks[$i]);
                            array_push($range,$service_checks[$i]);
                            array_push($service_ranges,$range);
                            $range = [];
                        }
                    }
                }

                // Make datasets        
                $ok = [];
                $warning = [];
                $critical = [];
                $unknown = [];

                for ($i=0; $i < sizeof($service_ranges); $i++) { 
                    
                    if ($i == 0) {
                        $service_name = $service_ranges[0][0]->service_name.' ('.$service_ranges[0][0]->host_name.')';
                    }

                    switch ($service_ranges[$i][0]->state) {
                        
                        case 0:
                            array_push($ok, [$service_ranges[$i][0]->start_time,$service_ranges[$i][1]->end_time]);
                            break;

                        case 1:
                            array_push($warning, [$service_ranges[$i][0]->start_time,$service_ranges[$i][1]->end_time]);
                            break;

                        case 2:
                            array_push($critical, [$service_ranges[$i][0]->start_time,$service_ranges[$i][1]->end_time]);
                            break;

                        case 3:
                            array_push($unknown, [$service_ranges[$i][0]->start_time,$service_ranges[$i][1]->end_time]);
                            break;
                    }
                }

                $data = [
                    'service_name' => $service_name,
                    'Ok' => $ok,
                    'Warning' => $warning,
                    'Critical' => $critical,
                    'Unknown' => $unknown,
                ];
            }

            array_push($datasets,$data);
        }

        return $datasets;

    }
}
