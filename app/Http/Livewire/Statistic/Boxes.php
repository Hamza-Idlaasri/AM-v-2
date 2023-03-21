<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Boxes extends Component
{
    public $box_name;
    public $date_from;
    public $date_to;
    public $site_name;

    public $boxes_up = 0;
    public $boxes_down = 0;
    public $boxes_unreachable = 0;

    public $boxes_status;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $this->getHistory();

        $this->boxes_status = [$this->boxes_up,$this->boxes_down,$this->boxes_unreachable];

        return view('livewire.statistic.boxes')
            ->with(['boxes_status' => $this->boxes_status, 'boxes_names' => $this->getBoxesNames()])
            ->extends('layouts.app')
            ->section('content');
    }

    // public function getStateRanges()
    // {
    //     $boxes_names = $this->getBoxesNames();

    //     $boxes_ranges = [];

    //     foreach ($boxes_names as $box) {

    //         $checks = $this->getBoxesNamesChecks()->where('nagios_hosts.host_object_id', $box->host_object_id)->get();

    //         if(!empty($checks)) {
    //             array_push($boxes_ranges, $checks);
    //         }

    //         unset($checks);
    //     }
        
    //     return $this->OrganizeStates($boxes_ranges);
    // }

    public function OrganizeStates($boxes_ranges)
    {
        $boxes_range_of_states = [];

        foreach ($boxes_ranges as $host) {
            
            // Get a single box checks
            $checks_of_box = $host;
            
            $start_index = 0;
            $end_index = 0;

            if (sizeof($checks_of_box) == 1) {
                // Convert State
                //$checks_of_box[0]->state = $this->convertState($checks_of_box[0]->state);
                // push the range in table
                array_push($boxes_range_of_states, $checks_of_box[0]->state);
            } else {
                // Search on single hosts checks ranges
                for ($i=0; $i < sizeof($checks_of_box); $i++) {
                    
                    if ($i < (sizeof($checks_of_box)-1)) {

                        if ($checks_of_box[$i]->state == $checks_of_box[$i+1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set end_time of host check to the last end_time of state
                            // $checks_of_box[$start_index]->end_time = $checks_of_box[$end_index]->end_time;

                            // Convert State
                            //$checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]->state);

                            // reset the start_index var
                            $start_index = $i+1;
                        }

                    } else {
                        if ($checks_of_box[$i]->state == $checks_of_box[$i-1]->state) {

                            // set end_time of host check to the last end_time of state
                            // $checks_of_box[$start_index]->end_time = $checks_of_box[$i]->end_time;
                            
                            // Convert State
                            //$checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]->state);

                        } else {
                            /**** BEFOR LAST INDEX */
                            // set end_time of host check to the last end_time of state
                            // $checks_of_box[$start_index]->end_time = $checks_of_box[$i-1]->end_time;
                            
                            // Convert State
                            //$checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]->state);

                            /**** LAST INDEX */
                            // Convert State
                            //$checks_of_box[$i]->state = $this->convertState($checks_of_box[$i]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$i]->state);
                        }
                    }

                }
            }
            
        }

        return $boxes_range_of_states;
    }

    public function SortStatus($ranges)
    {
        $this->boxes_up = 0;
        $this->boxes_down = 0;
        $this->boxes_unreachable = 0;
        
        foreach ($ranges as $state) {
            
            switch ($state) {
                case 0:
                    $this->boxes_up++;
                    break;
                case 1:
                    $this->boxes_down++;
                    break;
                case 2:
                    $this->boxes_unreachable++;
                    break;
            }
        }
    }

    // public function getBoxesNamesChecks()
    // {
        
    //     if ($this->site_name == 'All') {
            
    //         $boxes_histories = DB::table('nagios_hostchecks')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
    //             ->where('alias','box')
    //             ->select('nagios_hosts.display_name as box_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
    //             ->where('is_raw_check','=', 0)
    //             ->orderBy('nagios_hostchecks.start_time');
                
    //     } else {

    //         $boxes_histories = DB::table('nagios_hostchecks')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->where('alias','box')
    //             ->where('nagios_customvariables.varvalue',$this->site_name)
    //             ->select('nagios_hosts.display_name as box_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
    //             ->where('is_raw_check','=', 0)
    //             ->orderBy('nagios_hostchecks.start_time');
    //     }   

    //     // filter bu name
    //     if ($this->box_name) {
    //         $boxes_histories = $boxes_histories->where('nagios_hosts.display_name',$this->box_name);    
    //     }

    //     // filter by Date From
    //     if ($this->date_from)
    //     {
    //         $boxes_histories = $boxes_histories->where('nagios_hostchecks.start_time','>=',$this->date_from);
    //     }

    //     // filter by Date To
    //     if ($this->date_to)
    //     {
    //         $boxes_histories = $boxes_histories->where('nagios_hostchecks.start_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
    //     }

    //     $boxes_histories = $boxes_histories->take(20000);

    //     return $boxes_histories;
    // }

    public function getHistory()
    {
        $collection = collect();
        $last_state = [];

        if ($this->site_name == 'All') {

            $history = DB::table('nagios_statehistory')
                ->join('nagios_hosts','nagios_statehistory.object_id','=','nagios_hosts.host_object_id')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_hosts.address','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.output','nagios_statehistory.statehistory_id')
                ->where('alias','box')
                ->orderBy('nagios_statehistory.state_time');

        } else {
            
            $history = DB::table('nagios_statehistory')
                ->join('nagios_hosts','nagios_statehistory.object_id','=','nagios_hosts.host_object_id') 
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_hosts.address','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.output','nagios_statehistory.statehistory_id')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->orderBy('nagios_statehistory.state_time');
                
        }

        // filter by name
        if ($this->box_name) {
            $history = $history->where('nagios_hosts.display_name', $this->box_name);
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

        $history = $history->chunk(1000, function ($boxes_history) use (&$collection) {

                    $boxes_names = $this->getBoxesNames();

                    $boxes_ranges = [];

                    foreach ($boxes_names as $box) {

                        $checks = [];

                        foreach ($boxes_history as $history) {
                            if ($history->host_object_id == $box->host_object_id) {
                                array_push($checks, $history);
                            }
                        }

                        if(!empty($checks)) {
                            array_push($boxes_ranges, $checks);
                        }

                        unset($checks);
                    }
                    
                    
                    $ranges = $this->OrganizeStates($boxes_ranges);

                    foreach ($ranges as $range) {
                        $collection->push($range);
                    }

                });
    
        $boxes_current_state = $this->boxesCurrentState();

        foreach ($boxes_current_state as $box) {
            $collection->prepend($box->state);
        }
        
        return $this->SortStatus($collection);
        
    }

    public function getBoxesNames()
    {

        if ($this->site_name == 'All') {

            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();

        } else {

            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();
        }

    }

    public function boxesCurrentState()
    {
        if($this->site_name == "All")
        {
            $current_state = DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varname','SITE')
                ->select('nagios_hosts.host_object_id','nagios_hosts.display_name as box_name','nagios_hosts.address','nagios_hoststatus.current_state as state','nagios_hoststatus.last_check as state_time','nagios_hoststatus.output','nagios_customvariables.varvalue as site_name')
                ->orderBy('last_check');
        }
        else 
        {
            $current_state = DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.host_object_id','nagios_hosts.display_name as box_name','nagios_hosts.address','nagios_hoststatus.current_state as state','nagios_hoststatus.last_check as state_time','nagios_hoststatus.output')
                ->orderBy('last_check');
        }

        // filter by name
        if ($this->box_name) {
            $current_state = $current_state->where('nagios_hosts.display_name', $this->box_name);
        }

        // filter by Date From
        if ($this->date_from)
        {
            $current_state = $current_state->where('nagios_hoststatus.last_check','>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to)
        {
            $current_state = $current_state->where('nagios_hoststatus.last_check','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
        }

        return $current_state->get();
    }
}

