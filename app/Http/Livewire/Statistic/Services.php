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

    public $services_status;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $this->getHistory();
    
        $this->services_status = [$this->services_ok, $this->services_warning, $this->services_critical, $this->services_unknown];

        return view('livewire.statistic.services')
            ->with(['services_status' => $this->services_status, 'services_names' => $this->getServicesGroups()])
            ->extends('layouts.app')
            ->section('content');
    }

    // public function getStateRanges()
    // {
    //     $services_names = $this->ServicesNames();

    //     $services_ranges = [];

    //     foreach ($services_names as $service) {

    //         $checks = $this->getServicesChecks()->where('nagios_services.service_object_id', $service->service_object_id)->get();

    //         if(!empty($checks)) {
    //             array_push($services_ranges, $checks);
    //         }

    //         unset($checks);
    //     }
        
    //     $this->OrganizeStates($services_ranges);
    // }

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

        return $services_range_of_states;
    }

    public function SortStatus($ranges)
    {  
        $this->services_ok = 0;
        $this->services_warning = 0;
        $this->services_critical = 0;
        $this->services_unknown = 0;

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

    // public function getServicesChecks()
    // {
        
    //     if ($this->site_name == 'All') {
            
    //         $services_histories = DB::table('nagios_servicechecks')
    //             ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //             ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
    //             ->where('alias','host')
    //             ->orderBy('nagios_servicechecks.start_time');
                
    //     } else {

    //         $services_histories = DB::table('nagios_servicechecks')
    //             ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->where('nagios_customvariables.varvalue',$this->site_name)
    //             ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
    //             ->where('alias','host')
    //             ->orderBy('nagios_servicechecks.start_time');

    //     }
        
    //     // filter by name
    //     if ($this->service_name) {
    //         $services_histories = $services_histories->where('nagios_services.display_name',$this->service_name);    
    //     }

    //     // filter by Date From
    //     if ($this->date_from)
    //     {
    //         $services_histories = $services_histories->where('nagios_servicechecks.start_time','>=',$this->date_from);
    //     }

    //     // filter by Date To
    //     if ($this->date_to)
    //     {
    //         $services_histories = $services_histories->where('nagios_servicechecks.start_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
    //     }

    //     $services_histories = $services_histories->take(20000);

    //     return $services_histories;
    // }

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
        $services_groups = [];
        $all_groups = [];
        $hosts = $this->getHostsNames();
    
        $services = $this->getServicesNames();

        foreach ($hosts as $host) {

            foreach ($services as $key => $service) {

                if ($service->host_name == $host->host_name) {
                    array_push($services_groups, $service->service_name);
                }

            }

            array_push($all_groups, (object)['host_name' => $host->host_name, 'services_names' => $services_groups]);

            $services_groups = [];
        }
    
        return $all_groups;
    }

    public function getHostsNames()
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

    public function getServicesNames()
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

    public function getHistory()
    {
        $collection = collect();
        $last_state = [];

        if ($this->site_name == 'All') {

            $history = DB::table('nagios_statehistory')
                ->join('nagios_services','nagios_statehistory.object_id','=','nagios_services.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_statehistory.statehistory_id','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.state_time_usec','nagios_statehistory.output')
                ->where('alias','host')
                ->orderBy('nagios_statehistory.state_time');

        } else {
            
            $history = DB::table('nagios_statehistory')
                ->join('nagios_services','nagios_statehistory.object_id','=','nagios_services.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_statehistory.statehistory_id','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.state_time_usec','nagios_statehistory.output')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->orderBy('nagios_statehistory.state_time');
                
        }

        // filter by name
        if ($this->service_name) {
            $history = $history->where('nagios_services.display_name', $this->service_name);
        }

        // filter by Date From
        if ($this->date_from)
        {
            $history = $history->where('nagios_statehistory.state_time','>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to)
        {
            $history = $history->where('nagios_statehistory.state_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
        }

        $history = $history->chunk(1000, function ($services_history) use (&$collection) {

                    $services_names = $this->ServicesNames();

                    $services_ranges = [];

                    foreach ($services_names as $service) {

                        $checks = [];

                        foreach ($services_history as $history) {
                            if ($history->service_object_id == $service->service_object_id) {
                                array_push($checks, $history);
                            }
                        }

                        if(!empty($checks)) {
                            array_push($services_ranges, $checks);
                        }

                        unset($checks);
                    }
                    
                    
                    $ranges = $this->OrganizeStates($services_ranges);

                    foreach ($ranges as $range) {
                        $collection->push($range);
                    }

                });
    
        $services_current_state = $this->servicesCurrentState();

        foreach ($services_current_state as $service) {
            $collection->prepend($service->state);
        }

        return $this->SortStatus($collection);

    }

    public function servicesCurrentState()
    {
        if ($this->site_name == 'All') {

            $current_state = DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->select('nagios_servicestatus.current_state as state')
                ->orderBy('last_check');

        }
        else
        {
            $current_state = DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->select('nagios_servicestatus.current_state as state')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->orderBy('last_check');
        }

        // filter by name
        if ($this->service_name) {
            $current_state = $current_state->where('nagios_services.display_name', $this->service_name);
        }

        // filter by Date From
        if ($this->date_from)
        {
            $current_state = $current_state->where('nagios_servicestatus.last_check','>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to)
        {
            $current_state = $current_state->where('nagios_servicestatus.last_check','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
        }

        return $current_state->get();
    }
}
