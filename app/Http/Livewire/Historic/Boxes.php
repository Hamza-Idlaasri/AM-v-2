<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class Boxes extends Component
{
    use WithPagination;

    public $status = 'all';
    public $box_name;
    public $date_from;
    public $date_to;
    public $site_name;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        // $boxes_histories = $this->getHistory();
        $boxes_histories = $this->getBySQL();

        // filter by state
        // if($this->status != 'all')
        // {
        //     foreach ($boxes_histories as $key => $box) {
        //         if ($box->state == $this->status) {
        //             continue;
        //         } else {
        //             $boxes_histories->forget($key);
        //         }
        //     }

        // }    

        return view('livewire.historic.boxes')
            ->with(['boxes_histories' => $this->paginate($boxes_histories), 'boxes_names' => $this->getBoxesNames(), 'download' => $boxes_histories, "msg" => $this->description()])
            ->extends('layouts.app')
            ->section('content');
    }

    // public function getStateRanges()
    // {
    //     $boxes_names = $this->getBoxes();

    //     $boxes_ranges = [];

    //     foreach ($boxes_names as $box) {

    //         $checks = $this->getBoxesChecks()->where('nagios_hosts.host_object_id', $box->host_object_id)->get();

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

        foreach ($boxes_ranges as $box) {

            // Get a single box checks
            $checks_of_box = $box;

            $start_index = 0;
            $end_index = 0;

            if (sizeof($checks_of_box) == 1) {
                // Convert State
                //$checks_of_box[0]->state = $this->convertState($checks_of_box[0]->state);
                // push the range in table
                array_push($boxes_range_of_states, $checks_of_box[0]);
            } else {
                // Search on single box checks ranges
                for ($i = 0; $i < sizeof($checks_of_box); $i++) {

                    if ($i < (sizeof($checks_of_box) - 1)) {

                        if ($checks_of_box[$i]->state == $checks_of_box[$i + 1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set state_time of $box check to the last state_time of state
                            $checks_of_box[$start_index]->state_time = $checks_of_box[$end_index]->state_time;

                            // Convert State
                            //$checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]);

                            // reset the start_index var
                            $start_index = $i + 1;
                        }
                    } else {
                        if ($checks_of_box[$i]->state == $checks_of_box[$i - 1]->state) {

                            // set state_time of $box check to the last state_time of state
                            $checks_of_box[$start_index]->state_time = $checks_of_box[$i]->state_time;

                            // Convert State
                            //$checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]);
                        } else {
                            /**** BEFOR LAST INDEX */
                            // set state_time of $box check to the last state_time of state
                            $checks_of_box[$start_index]->state_time = $checks_of_box[$i - 1]->state_time;

                            // Convert State
                            //$checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            // array_push($boxes_range_of_states, $checks_of_box[$start_index]);

                            /**** LAST INDEX */
                            // Convert State
                            //$checks_of_box[$i]->state = $this->convertState($checks_of_box[$i]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$i]);
                        }
                    }
                }
            }
        }

        return $boxes_range_of_states;
    }

    public function OrderRanges($ranges)
    {
        return $ranges->sortByDesc('state_time');
    }

    // public function getBoxesChecks()
    // {

    //     if ($this->site_name == 'All') {

    //         $boxes_histories = DB::table('nagios_hostchecks')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
    //             ->where('alias','box')
    //             ->select('nagios_hosts.display_name as box_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.statehistory_id','nagios_hostchecks.state','nagios_hostchecks.state_time','nagios_hostchecks.state_time','nagios_hostchecks.output')
    //             ->where('is_raw_check','=', 0)
    //             ->orderBy('nagios_hostchecks.state_time');

    //     } else {

    //         $boxes_histories = DB::table('nagios_hostchecks')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->where('alias','box')
    //             ->where('nagios_customvariables.varvalue',$this->site_name)
    //             ->select('nagios_hosts.display_name as box_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.statehistory_id','nagios_hostchecks.state','nagios_hostchecks.state_time','nagios_hostchecks.state_time','nagios_hostchecks.output')
    //             ->where('is_raw_check','=', 0)
    //             ->orderBy('nagios_hostchecks.state_time');

    //     }   

    //     // filter by name
    //     if ($this->box_name) {
    //         $boxes_histories = $boxes_histories->where('nagios_hosts.display_name',$this->box_name);
    //     }

    //     // filter by Date From
    //     if ($this->date_from)
    //     {
    //         $boxes_histories = $boxes_histories->where('nagios_hostchecks.state_time','>=',$this->date_from);
    //     }

    //     // filter by Date To
    //     if ($this->date_to)
    //     {
    //         $boxes_histories = $boxes_histories->where('nagios_hostchecks.state_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
    //     }

    //     $boxes_histories = $boxes_histories->take(20000);

    //     return $boxes_histories;
    // }

    public function getBoxesNames()
    {

        if ($this->site_name == 'All') {

            return DB::table('nagios_hosts')
                ->where('alias', 'box')
                ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();
        } else {

            return DB::table('nagios_hosts')
                ->where('alias', 'box')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();
        }
    }

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function getHistory()
    {
        $collection = collect();
        $last_state = [];

        if ($this->site_name == 'All') {

            $history = DB::table('nagios_statehistory')
                ->join('nagios_hosts', 'nagios_statehistory.object_id', '=', 'nagios_hosts.host_object_id')
                ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.host_object_id', 'nagios_hosts.address', 'nagios_statehistory.last_state', 'nagios_statehistory.state', 'nagios_statehistory.state_time', 'nagios_statehistory.output', 'nagios_statehistory.statehistory_id')
                ->where('alias', 'box')
                ->orderBy('nagios_statehistory.state_time');
        } else {

            $history = DB::table('nagios_statehistory')
                ->join('nagios_hosts', 'nagios_statehistory.object_id', '=', 'nagios_hosts.host_object_id')
                ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.host_object_id', 'nagios_hosts.address', 'nagios_statehistory.last_state', 'nagios_statehistory.state', 'nagios_statehistory.state_time', 'nagios_statehistory.output', 'nagios_statehistory.statehistory_id')
                ->where('alias', 'box')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->orderBy('nagios_statehistory.state_time');
        }

        // filter by name
        if ($this->box_name) {
            $history = $history->where('nagios_hosts.display_name', $this->box_name);
        }

        // filter by Date From
        if ($this->date_from) {
            $history = $history->where('nagios_statehistory.state_time', '>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to) {
            $history = $history->where('nagios_statehistory.state_time', '<=', date('Y-m-d', strtotime($this->date_to . ' + 1 days')));
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

                if (!empty($checks)) {
                    array_push($boxes_ranges, $checks);
                }

                unset($checks);
            }


            $ranges = $this->OrganizeStates($boxes_ranges);

            foreach ($ranges as $range) {
                $collection->push($range);
            }
        });

        $collection = clone $this->OrderRanges($collection);

        // $boxes_current_state = $this->boxesCurrentState();

        // foreach ($boxes_current_state as $box) {
        //     $collection->prepend($box);
        // }

        return $collection;
    }

    public function description()
    {
        return ['fonction normalement', 'le box est OFF', 'difficulté à reconnaître l\'état du box, vérifier si le box est ON'];
    }

    public function boxesCurrentState()
    {
        if ($this->site_name == "All") {
            $current_state = DB::table('nagios_hosts')
                ->where('alias', 'box')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->join('nagios_hoststatus', 'nagios_hosts.host_object_id', '=', 'nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varname', 'SITE')
                ->select('nagios_hosts.host_object_id', 'nagios_hosts.display_name as box_name', 'nagios_hosts.address', 'nagios_hoststatus.current_state as state', 'nagios_hoststatus.last_check as start_time', 'nagios_hoststatus.output', 'nagios_customvariables.varvalue as site_name')
                ->orderBy('last_check');
        } else {
            $current_state = DB::table('nagios_hosts')
                ->where('alias', 'box')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->join('nagios_hoststatus', 'nagios_hosts.host_object_id', '=', 'nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->select('nagios_hosts.host_object_id', 'nagios_hosts.display_name as box_name', 'nagios_hosts.address', 'nagios_hoststatus.current_state as state', 'nagios_hoststatus.last_check as start_time', 'nagios_hoststatus.output')
                ->orderBy('last_check');
        }

        // filter by name
        if ($this->box_name) {
            $current_state = $current_state->where('nagios_hosts.display_name', $this->box_name);
        }

        // filter by Date From
        if ($this->date_from) {
            $current_state = $current_state->where('nagios_hoststatus.last_check', '>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to) {
            $current_state = $current_state->where('nagios_hoststatus.last_check', '<=', date('Y-m-d', strtotime($this->date_to . ' + 1 days')));
        }

        // filter by status
        if ($this->status != 'all') {
            $current_state = $current_state->where('nagios_hoststatus.current_state', $this->status);
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
                ->where('nagios_hosts.alias', 'box')
                ->orderBy('nagios_statehistory.object_id')
                ->orderBy('nagios_statehistory.state_time')
                ->groupBy('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_statehistory.output', 'nagios_hosts.display_name', 'nagios_hosts.address');
        } else {

            $history = DB::table('nagios_statehistory')
                ->select('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_statehistory.output', 'nagios_hosts.display_name as box_name', 'nagios_hosts.address')
                ->selectRaw('MIN(nagios_statehistory.state_time) AS start_time')
                ->selectRaw('MAX(nagios_statehistory.state_time) AS end_time')
                ->selectRaw('TIMEDIFF(MAX(nagios_statehistory.state_time), MIN(nagios_statehistory.state_time)) AS duration')
                ->selectRaw('@group_number := @group_number + IF(@prev_state = nagios_statehistory.state, 0, 1) AS state_group')
                ->selectRaw('@prev_state := nagios_statehistory.state')
                ->join('nagios_hosts', 'nagios_statehistory.object_id', '=', 'nagios_hosts.host_object_id')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->where('nagios_hosts.alias', 'box')
                ->orderBy('nagios_statehistory.object_id')
                ->orderBy('nagios_statehistory.state_time')
                ->groupBy('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_statehistory.output', 'nagios_hosts.display_name', 'nagios_hosts.address');
        }

        // filter by name
        if ($this->box_name) {
            $history = $history->where('nagios_hosts.display_name', $this->box_name);
        }

        // filter by Date From
        if ($this->date_from) {
            $history = $history->where('nagios_statehistory.state_time', '>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to) {
            $history = $history->where('nagios_statehistory.state_time', '<=', date('Y-m-d', strtotime($this->date_to . ' + 1 days')));
        }

        // filter by state
        if ($this->status != 'all') {
            $history = $history->where('nagios_statehistory.state', $this->status);
        }

        $history = $history->get();

        // Get Current State
        $current_state = $this->boxesCurrentState();

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

        return $history;
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
}
