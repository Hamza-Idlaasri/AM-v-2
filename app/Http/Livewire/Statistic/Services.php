<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Services extends Component
{
    public $service_name;
    public $date_from;
    public $date_to;
    public $site_name;

    public $services_ok = 0;
    public $services_warning = 0;
    public $services_critical = 0;
    public $services_unknown = 0;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $this->getStateRanges();
    
        $services_status = [$this->services_ok, $this->services_warning, $this->services_critical, $this->services_unknown];

        return view('livewire.statistic.services')
            ->with(['services_status' => $services_status, 'services_names' => $this->getServicesGroups()])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getStateRanges()
    {
        $services_names = $this->ServicesNames();

        $services_ranges = [];

        foreach ($services_names as $service) {

            $checks = $this->getServicesChecks()->where('nagios_services.service_object_id', $service->service_object_id)->get();

            if(!empty($checks)) {
                array_push($services_ranges, $checks);
            }

            unset($checks);
        }
        
        $this->OrganizeStates($services_ranges);
    }

    public function OrganizeStates($services_ranges)
    {
        $services_range_of_states = [];

        foreach ($services_ranges as $service) {
            
            // Get a single serviceschecks
            $checks_of_service = $service;
            
            $start_index = 0;
            $end_index = 0;

            if (sizeof($checks_of_service) == 1) {
                // push the state in table
                array_push($services_range_of_states, $checks_of_service[0]->state);
            } else {
                // Search on single services checks ranges
                for ($i=0; $i < sizeof($checks_of_service); $i++) {
                    
                    if ($i < (sizeof($checks_of_service)-1)) {

                        if ($checks_of_service[$i]->state == $checks_of_service[$i+1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set end_time of equip check to the last end_time of state
                            // $checks_of_service[$start_index]->end_time = $checks_of_service[$end_index]->end_time;

                            // push the state in table
                            array_push($services_range_of_states, $checks_of_service[$start_index]->state);

                            // reset the start_index var
                            $start_index = $i+1;
                        }

                    } else {
                        if ($checks_of_service[$i]->state == $checks_of_service[$i-1]->state) {

                            // set end_time of equip check to the last end_time of state
                            // $checks_of_service[$start_index]->end_time = $checks_of_service[$i]->end_time;

                            // push the state in table
                            array_push($services_range_of_states, $checks_of_service[$start_index]->state);

                        } else {
                            /**** BEFOR LAST INDEX */
                            // set end_time of equip check to the last end_time of state
                            // $checks_of_service[$start_index]->end_time = $checks_of_service[$i-1]->end_time;

                            // push the state in table
                            array_push($services_range_of_states, $checks_of_service[$start_index]->state);

                            /**** LAST INDEX */
                            // push the state in table
                            array_push($services_range_of_states, $checks_of_service[$i]->state);
                        }
                    }

                }
            }
            
        }

        return $this->SortStatus($services_range_of_states);
    }

    public function SortStatus($ranges)
    {  
        foreach ($ranges as $state) {
            
            switch ($state) {
                case 0:
                    $this->services_ok++;
                    break;
                case 1:
                    $this->services_warning++;
                    break;
                case 2:
                    $this->services_critical++;
                    break;
                case 3:
                    $this->services_unknown++;
                    break;
            }
        }

    }

    public function getServicesChecks()
    {
        
        if ($this->site_name == 'All') {
            
            $services_histories = DB::table('nagios_servicechecks')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                ->where('alias','host')
                ->orderBy('nagios_servicechecks.start_time');
                
        } else {

            $services_histories = DB::table('nagios_servicechecks')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                ->where('alias','host')
                ->orderBy('nagios_servicechecks.start_time');

        }
        
        // filter by name
        if ($this->service_name) {
            $services_histories = $services_histories->where('nagios_services.display_name',$this->service_name);    
        }

        // filter by Date From
        if ($this->date_from)
        {
            $services_histories = $services_histories->where('nagios_servicechecks.start_time','>=',$this->date_from);
        }

        // filter by Date To
        if ($this->date_to)
        {
            $services_histories = $services_histories->where('nagios_servicechecks.start_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
        }

        $services_histories = $services_histories->take(20000);

        return $services_histories;
    }

    public function ServicesNames()
    {
        
        if ($this->site_name == 'All') {

            return DB::table('nagios_services')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_hosts.host_object_id','nagios_hosts.display_name as host_name')
                ->where('alias','host')
                ->get();

        } else {
        
            return DB::table('nagios_services')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_hosts.host_object_id','nagios_hosts.display_name as host_name')
                ->where('alias','host')
                ->get();

        }
    }

    public function getServicesGroups()
    {
        $groups = [];
        $hosts = $this->getServices();
        $all_groups = [];
    
        foreach ($hosts as $host) {
    
            $group = [];
    
            foreach ($this->ServicesNames() as $service) {
    
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

    public function getServices()
    {

        if ($this->site_name == 'All') {

            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();

        } else {

            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();
        }

    }
}
