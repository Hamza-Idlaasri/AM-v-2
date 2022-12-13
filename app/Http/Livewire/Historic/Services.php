<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Services extends Component
{
    public $status = 'all';
    public $service_name;
    public $date_from;
    public $date_to;

    // protected $queryString = ['status','service_name','date_from','date_to'];

    public function render()
    {
        $services_names = $this->getServicesName()->get();
        
        $services_histories = [];

        foreach ($services_names as $service) {

            $all_services_checks = $this->getServicesChecks()
                ->where('nagios_servicechecks.service_object_id','=',$service->service_object_id)
                ->get();

            if(sizeof($all_services_checks))
            {
                $status = $this->getStatus($all_services_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $service_checks = $this->getServicesChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][0])->get();
                    
                    $end_host_checks = $this->getServicesChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][1])->get();

                    $service_checks[0]->end_time = $end_host_checks[0]->end_time;

                    array_push($services_histories,$service_checks[0]);
                }

            } else {
                            
                continue;
            }

        }

        if($this->status != 'all')
        {
            $services_histories = $this->filterByStatus($services_histories,$this->status);
        }
            
        if ($this->service_name) 
        {
            $services_histories = $this->filterByName($services_histories,$this->service_name);
        }

        foreach ($services_histories as $services) {

            unset($services->alias);
            unset($services->host_object_id);
            unset($services->service_object_id);
            unset($services->servicecheck_id);

            switch ($services->state) {
                case 0:
                    $services->state = 'Ok';
                    break;
                case 1:
                    $services->state = 'Warning';
                    break;
                case 2:
                    $services->state = 'Critical';
                    break;
                case 3:
                    $services->state = 'Unknown';
                    break;
            }
        }

        return view('livewire.historic.services')
            ->with(['services_histories' => $services_histories,'services_names' => $this->getServicesGroups($services_names)])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getServicesChecks()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $from_date = $this->date_from;
        $to_date = $this->date_to;
        
        // Check from date
        if(empty($this->date_from))
        {
            $from_date = date('Y-m-d H:i:s', strtotime('-24 hours', time()));

        } else {
            $from_date = date('Y-m-d H:i:s', strtotime($from_date));
        }
        
        // Check to date
        if(empty($to_date))
        {
            if(empty($this->date_from))
            {
                $to_date = date('Y-m-d H:i:s');
                
            } else {
                $to_date = date('Y-m-d H:i:s', strtotime($from_date.'+24 hours'));
            }

        } else {
            if(empty($this->date_from))
            {
                $to_date = date('Y-m-d H:i:s', strtotime($to_date.'+22 hours'));
                $from_date = date('Y-m-d H:i:s', strtotime($to_date.'-24 hours'));
            } else {
                // TEMPRORY SOLUTION
                $to_date = date('Y-m-d H:i:s', strtotime($to_date.'+22 hours'));
                $from_date = date('Y-m-d H:i:s', strtotime($to_date.'-24 hours'));
            }
        }

        if ($site_name == 'All') {
            
            return DB::table('nagios_servicechecks')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_hosts.alias','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                ->where('alias','host')
                ->where('nagios_servicechecks.end_time','>=',$from_date)
                ->where('nagios_servicechecks.end_time','<=',$to_date);
        }
        else
        {
            return DB::table('nagios_servicechecks')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.alias','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                ->where('alias','host')
                ->where('nagios_servicechecks.end_time','>=',$from_date)
                ->where('nagios_servicechecks.end_time','<=',$to_date);
        }
    }

    public function getServicesName()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == 'All') {
            
            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_hosts.host_object_id','nagios_hosts.display_name as host_name','nagios_services.service_object_id','nagios_services.display_name as service_name');
        }
        else
        {
            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.host_object_id','nagios_hosts.display_name as host_name','nagios_services.service_object_id','nagios_services.display_name as service_name');
        }
        
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

    public function filterByStatus($services_histories,$status)
    {
        $services_filtred = [];

        switch ($status) {
            case 'ok':
                $status = 0;
                break;
            case 'warning':
                $status = 1;
                break;
            case 'critical':
                $status = 2;
                break;
            case 'unknown':
                $status = 3;
                break;
        }

        foreach ($services_histories as $service) {
            
            if($service->state == $status)
            {
                array_push($services_filtred,$service);
            }

        }

        return $services_filtred;
    }

    public function filterByName($services_histories,$name)
    {
        $services_filtred = [];

        foreach ($services_histories as $service) {
            
            if($service->service_name == $name)
            {
                array_push($services_filtred,$service);
            }

        }

        return $services_filtred;
    }

    // public function filterByDateFrom($services_histories,$date_from)
    // {
    //     $services_filtred = [];

    //     foreach ($services_histories as $service) {
            
    //         if($service->start_time >= $this->date_from)
    //         {
    //             array_push($services_filtred,$service);
    //         }

    //     }
    
    //     return $services_filtred;
    // }

    // public function filterByDateTo($services_histories,$date_to)
    // {
    //     $services_filtred = [];

    //     foreach ($services_histories as $service) {
            
    //         if($service->end_time <= $this->date_to)
    //         {
    //             array_push($services_filtred,$service);
    //         }

    //     }
    
    //     return $services_filtred;
    // }

    public function getServicesGroups($services_names)
    {
        $groups = [];
        $hosts = $this->getHosts();
        $all_groups = [];

        foreach ($hosts as $host) {

            $group = [];

            foreach ($services_names as $service) {
                
                if($service->host_object_id == $host->host_object_id)
                {
                    array_push($group,$service);
                } 
            }

            array_push($groups,$group);
        }

        $services = [];

        for ($i=0; $i < sizeof($groups); $i++) {
        
            foreach ($groups[$i] as $gp) {
                
                array_push($services,$gp->service_name);

            }

            array_push($all_groups,(object)['host_name' => $groups[$i][0]->host_name, 'services' => $services]);

            $services = [];
        }

        return $all_groups;
    }

    public function getHosts()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == 'All') {
            
            return DB::table('nagios_hosts')
            ->where('alias','host')
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
            ->orderBy('display_name')
            ->get();

        }
        else
        {
            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();
        }
       
    }
}
