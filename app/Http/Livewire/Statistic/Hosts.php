<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use Carbon\Carbon;

class Hosts extends Component
{
    // Site Name
    public $site_name;

    // Filter
    public $host_name;
    public $date_from;
    public $date_to;

    // Statistics
    public $hosts_up = 0;
    public $hosts_down = 0;
    public $hosts_unreachable = 0;

    public $hosts_status;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        // $this->getHistory();
        $this->getBySQL();

        $this->hosts_status = [$this->hosts_up, $this->hosts_down, $this->hosts_unreachable];

        return view('livewire.statistic.hosts')
            ->with(['hosts_status' => $this->hosts_status, 'hosts_names' => $this->getHosts()])
            ->extends('layouts.app')
            ->section('content');
    }

    // public function getStateRanges()
    // {
    //     $hosts_names = $this->getHosts();

    //     $hosts_ranges = [];

    //     foreach ($hosts_names as $host) {

    //         $checks = $this->getHostsChecks()->where('nagios_hosts.host_object_id', $host->host_object_id)->get();

    //         if(!empty($checks)) {
    //             array_push($hosts_ranges, $checks);
    //         }

    //         unset($checks);
    //     }
        
    //     return $this->OrganizeStates($hosts_ranges);
    // }

    public function OrganizeStates($hosts_ranges)
    {
        $hostes_range_of_states = [];

        foreach ($hostes_ranges as $host) {
            
            // Get a single host checks
            $checks_of_host = $host;
            
            $start_index = 0;
            $end_index = 0;

            if (sizeof($checks_of_host) == 1) {
                // Convert State
                //$checks_of_host[0]->state = $this->convertState($checks_of_host[0]->state);
                // push the range in table
                array_push($hostes_range_of_states, $checks_of_host[0]);
            } else {
                // Search on single hosts checks ranges
                for ($i=0; $i < sizeof($checks_of_host); $i++) {
                    
                    if ($i < (sizeof($checks_of_host)-1)) {

                        if ($checks_of_host[$i]->state == $checks_of_host[$i+1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set state_time of host check to the last state_time of state
                            $checks_of_host[$start_index]->state_time = $checks_of_host[$end_index]->state_time;

                            // Convert State
                            //$checks_of_host[$start_index]->state = $this->convertState($checks_of_host[$start_index]->state);

                            // push the range in table
                            array_push($hostes_range_of_states, $checks_of_host[$start_index]);

                            // reset the start_index var
                            $start_index = $i+1;
                        }

                    } else {
                        if ($checks_of_host[$i]->state == $checks_of_host[$i-1]->state) {

                            // set state_time of host check to the last state_time of state
                            $checks_of_host[$start_index]->state_time = $checks_of_host[$i]->state_time;
                            
                            // Convert State
                            //$checks_of_host[$start_index]->state = $this->convertState($checks_of_host[$start_index]->state);

                            // push the range in table
                            array_push($hostes_range_of_states, $checks_of_host[$start_index]);

                        } else {
                            /**** BEFOR LAST INDEX */
                            // set state_time of host check to the last state_time of state
                            $checks_of_host[$start_index]->state_time = $checks_of_host[$i-1]->state_time;
                            
                            // Convert State
                            //$checks_of_host[$start_index]->state = $this->convertState($checks_of_host[$start_index]->state);

                            // push the range in table
                            array_push($hostes_range_of_states, $checks_of_host[$start_index]);

                            /**** LAST INDEX */
                            // Convert State
                            //$checks_of_host[$i]->state = $this->convertState($checks_of_host[$i]->state);

                            // push the range in table
                            array_push($hostes_range_of_states, $checks_of_host[$i]);
                        }
                    }

                }
            }
            
        }

        return $hostes_range_of_states;
    }

    // public function SortStatus($ranges)
    // {
    //     $this->hosts_up = 0;
    //     $this->hosts_down = 0;
    //     $this->hosts_unreachable = 0;
        
    //     foreach ($ranges as $state) {
           
    //         switch ($state) {
    //             case 0:
    //                 $this->hosts_up++;
    //                 break;
    //             case 1:
    //                 $this->hosts_down++;
    //                 break;
    //             case 2:
    //                 $this->hosts_unreachable++;
    //                 break;
    //         }
    //     }
    // }

    // public function getHostsChecks()
    // {
        
    //     if ($this->site_name == 'All') {
            
    //         $hosts_histories = DB::table('nagios_hostchecks')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
    //             ->where('alias','host')
    //             ->select('nagios_hosts.display_name as host_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
    //             ->where('is_raw_check','=', 0)
    //             ->orderBy('nagios_hostchecks.start_time');
                
    //     } else {

    //         $hosts_histories = DB::table('nagios_hostchecks')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->where('alias','host')
    //             ->where('nagios_customvariables.varvalue',$this->site_name)
    //             ->select('nagios_hosts.display_name as host_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
    //             ->where('is_raw_check','=', 0)
    //             ->orderBy('nagios_hostchecks.start_time');
    //     }   

    //     // filter bu name
    //     if ($this->host_name) {
    //         $hosts_histories = $hosts_histories->where('nagios_hosts.display_name',$this->host_name);    
    //     }

    //     // filter by Date From
    //     if ($this->date_from)
    //     {
    //         $hosts_histories = $hosts_histories->where('nagios_hostchecks.start_time','>=',$this->date_from);
    //     }

    //     // filter by Date To
    //     if ($this->date_to)
    //     {
    //         $hosts_histories = $hosts_histories->where('nagios_hostchecks.start_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
    //     }

    //     $hosts_histories = $hosts_histories->take(20000);

    //     return $hosts_histories;
    // }

    public function getHosts()
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

    public function getHistory()
    {
        $collection = collect();
        $last_state = [];

        if ($this->site_name == 'All') {

            $history = DB::table('nagios_statehistory')
                ->join('nagios_hosts','nagios_statehistory.object_id','=','nagios_hosts.host_object_id')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_hosts.address','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.output','nagios_statehistory.statehistory_id')
                ->where('alias','host')
                ->orderBy('nagios_statehistory.state_time');

        } else {
            
            $history = DB::table('nagios_statehistory')
                ->join('nagios_hosts','nagios_statehistory.object_id','=','nagios_hosts.host_object_id') 
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_hosts.address','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.output','nagios_statehistory.statehistory_id')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->orderBy('nagios_statehistory.state_time');
                
        }

        // filter by name
        if ($this->host_name) {
            $history = $history->where('nagios_hosts.display_name', $this->host_name);
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

        $history = $history->chunk(1000, function ($hosts_history) use (&$collection) {

                    $hosts_names = $this->getBoxesNames();

                    $hosts_ranges = [];

                    foreach ($hosts_names as $host) {

                        $checks = [];

                        foreach ($hosts_history as $history) {
                            if ($history->host_object_id == $host->host_object_id) {
                                array_push($checks, $history);
                            }
                        }

                        if(!empty($checks)) {
                            array_push($hosts_ranges, $checks);
                        }

                        unset($checks);
                    }
                    
                    
                    $ranges = $this->OrganizeStates($hosts_ranges);

                    foreach ($ranges as $range) {
                        $collection->push($range);
                    }

                });
    
        // $collection = clone $this->OrderRanges($collection);

        $hosts_current_state = $this->hostsCurrentState();
        
        foreach ($hosts_current_state as $host) {
            $collection->prepend($host->state);
        }
    
        return $this->SortStatus($collection);
        
    }

    // public function hostsCurrentState()
    // {
    //     if($this->site_name == "All")
    //     {
    //         $current_state = DB::table('nagios_hosts')
    //             ->where('alias','host')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
    //             ->where('nagios_customvariables.varname','SITE')
    //             ->select('nagios_hoststatus.current_state as state');
    //     }
    //     else 
    //     {
    //         $current_state = DB::table('nagios_hosts')
    //             ->where('alias','host')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
    //             ->where('nagios_customvariables.varvalue',$this->site_name)
    //             ->select('nagios_hoststatus.current_state as state');
    //     }

    //     // filter by name
    //     if ($this->host_name) 
    //     {
    //         $current_state = $current_state->where('nagios_hosts.display_name', $this->host_name);
    //     }

    //     // filter by Date From
    //     if ($this->date_from)
    //     {
    //         $current_state = $current_state->where('nagios_hoststatus.last_check','>=', $this->date_from);
    //     }

    //     // filter by Date To
    //     if ($this->date_to)
    //     {
    //         $current_state = $current_state->where('nagios_hoststatus.last_check','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
    //     }

    //     return $current_state->get();
    // }

    // Update

    public function hostsCurrentState()
    {
        if ($this->site_name == "All") {
            $current_state = DB::table('nagios_hosts')
                ->where('alias', 'host')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->join('nagios_hoststatus', 'nagios_hosts.host_object_id', '=', 'nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varname', 'SITE')
                ->select('nagios_hosts.host_object_id', 'nagios_hosts.display_name as host_name', 'nagios_hosts.address', 'nagios_hoststatus.current_state as state', 'nagios_hoststatus.last_check as start_time', 'nagios_hoststatus.output', 'nagios_customvariables.varvalue as site_name')
                ->orderBy('last_check');
        } else {
            $current_state = DB::table('nagios_hosts')
                ->where('alias', 'host')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->join('nagios_hoststatus', 'nagios_hosts.host_object_id', '=', 'nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->select('nagios_hosts.host_object_id', 'nagios_hosts.display_name as host_name', 'nagios_hosts.address', 'nagios_hoststatus.current_state as state', 'nagios_hoststatus.last_check as start_time', 'nagios_hoststatus.output')
                ->orderBy('last_check');
        }

        // filter by name
        if ($this->host_name) {
            $current_state = $current_state->where('nagios_hosts.display_name', $this->host_name);
        }

        // filter by Date From
        if ($this->date_from) {
            $current_state = $current_state->where('nagios_hoststatus.last_check', '>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to) {
            $current_state = $current_state->where('nagios_hoststatus.last_check', '<=', date('Y-m-d', strtotime($this->date_to . ' + 1 days')));
        }

        return $current_state->get();
    }

    public function getBySQL()
    {

        if ($this->site_name == 'All') {

            $history = DB::table('nagios_statehistory')
                ->select('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_statehistory.output', 'nagios_hosts.display_name as box_name', 'nagios_hosts.address')
                ->selectRaw('MIN(nagios_statehistory.state_time) AS start_time')
                ->selectRaw('MAX(nagios_statehistory.state_time) AS end_time')
                ->selectRaw('TIMEDIFF(MAX(nagios_statehistory.state_time), MIN(nagios_statehistory.state_time)) AS duration')
                ->selectRaw('@group_number := @group_number + IF(@prev_state = nagios_statehistory.state, 0, 1) AS state_group')
                ->selectRaw('@prev_state := nagios_statehistory.state')
                ->join('nagios_hosts', 'nagios_statehistory.object_id', '=', 'nagios_hosts.host_object_id')
                ->where('nagios_hosts.alias', 'host')
                ->orderBy('nagios_statehistory.object_id')
                ->orderBy('nagios_statehistory.state_time')
                ->groupBy('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_statehistory.output', 'nagios_hosts.display_name', 'nagios_hosts.address');
        } else {

            $history = DB::table('nagios_statehistory')
                ->select('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_statehistory.output', 'nagios_hosts.display_name as host_name', 'nagios_hosts.address')
                ->selectRaw('MIN(nagios_statehistory.state_time) AS start_time')
                ->selectRaw('MAX(nagios_statehistory.state_time) AS end_time')
                ->selectRaw('TIMEDIFF(MAX(nagios_statehistory.state_time), MIN(nagios_statehistory.state_time)) AS duration')
                ->selectRaw('@group_number := @group_number + IF(@prev_state = nagios_statehistory.state, 0, 1) AS state_group')
                ->selectRaw('@prev_state := nagios_statehistory.state')
                ->join('nagios_hosts', 'nagios_statehistory.object_id', '=', 'nagios_hosts.host_object_id')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->where('nagios_hosts.alias', 'host')
                ->orderBy('nagios_statehistory.object_id')
                ->orderBy('nagios_statehistory.state_time')
                ->groupBy('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_statehistory.output', 'nagios_hosts.display_name', 'nagios_hosts.address');
        }

        // filter by name
        if ($this->host_name) {
            $history = $history->where('nagios_hosts.display_name', $this->host_name);
        }

        // filter by Date From
        if ($this->date_from) {
            $history = $history->where('nagios_statehistory.state_time', '>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to) {
            $history = $history->where('nagios_statehistory.state_time', '<=', date('Y-m-d', strtotime($this->date_to . ' + 1 days')));
        }


        $history = $history->get();

        // Get Current State
        $current_state = $this->hostsCurrentState();

        // Add Current state to the historical data
        foreach ($current_state as $element) {

            // Get the last state of the element from statehistory table
            $last_state = $this->getStateHistory($element->host_object_id);

            // if the element has a historical data
            if ($last_state) {

                // if the current state is like the last historical state of the element
                if ($element->state == $last_state->state) {
                    // Last historical state
                    $last_historcal_state = $history->where('object_id', $element->host_object_id)->first();

                    // set the start and end time
                    $start_time = Carbon::parse($last_historcal_state->start_time);
                    $end_time = Carbon::parse($element->start_time);

                    // Calcule duration
                    $duration = $start_time->diff($end_time);

                    // Update the end_time of the historical data
                    $last_historcal_state->end_time = $element->start_time;

                    // Update the duration of the last historical state
                    $last_historcal_state->duration = $duration->format('%H:%i:%s');
                } else {
                    // Get the last historical state
                    $last_historcal_state = $history->where('object_id', $element->host_object_id)->first();

                    // Give the end_time to the current_state
                    $element->end_time = $element->start_time;

                    // Give the start_time of the current_state the end_time of the historical state
                    $element->start_time = $last_historcal_state->end_time;

                    // Calculate the duration
                    $element->duration = Carbon::parse($element->start_time)->diff(Carbon::parse($element->end_time))->format('%H:%i:%s');

                    // Push at the top of the $history collection
                    $history->prepend($element);
                }
            } else {

                // Get the first check's start_time
                $first_check = $this->getTheFirstCheck($element->host_object_id);

                // Give the end_time of checking to the current_state element
                $element->end_time = $element->start_time;

                // Give the start_time of checking to the current_state element
                $element->start_time = $first_check->start_time;

                // Calcule the duration
                $element->duration = Carbon::parse($element->start_time)->diff(Carbon::parse($element->end_time))->format('%H:%i:%s');

                $history->prepend($element);
            }
        }

        $this->SortStatus($history);
    }

    public function getStateHistory($object_id)
    {
        return DB::table('nagios_statehistory')
            ->where('object_id', $object_id)
            ->select('state', 'state_time')
            ->orderByDesc('state_time')
            ->first();
    }

    public function getTheFirstCheck($host_object_id)
    {
        return DB::table('nagios_hostchecks')
            ->where('host_object_id', $host_object_id)
            ->select('state', 'start_time')
            ->orderBy('start_time')
            ->first();
    }

    public function SortStatus($hosts)
    {
        $this->hosts_up = 0;
        $this->hosts_down = 0;
        $this->hosts_unreachable = 0;
        
        foreach ($hosts as $host) {
           
            switch ($host->state) {
                case 0:
                    $this->hosts_up++;
                    break;
                case 1:
                    $this->hosts_down++;
                    break;
                case 2:
                    $this->hosts_unreachable++;
                    break;
            }
        }
    }
}
