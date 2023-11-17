<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class Equips extends Component
{
    use WithPagination;

    public $status = 'all';
    public $equip_name;
    public $pin_nbr;
    public $date_from;
    public $date_to;
    public $site_name;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        $equips_histories = $this->getBySQL();

        return view('livewire.historic.equips')
            ->with(['equips_histories' => $this->paginate($equips_histories), 'equips_names' => $this->getEquipsGroups(), 'download' => $equips_histories])
            ->extends('layouts.app')
            ->section('content');
    }

    public function OrganizeStates($equips_ranges)
    {
        $equips_range_of_states = [];

        foreach ($equips_ranges as $range) {

            $start_index = 0;
            $end_index = 0;

            if (sizeof($range) == 1) {
                // Convert State
                //$range[0]->state = $this->convertState($range[0]->state);
                // push the range in table
                array_push($equips_range_of_states, $range[0]);
            } else {
                // Search on single equipements checks ranges
                for ($i = 0; $i < sizeof($range); $i++) {

                    if ($i < (sizeof($range) - 1)) {

                        if ($range[$i]->state == $range[$i + 1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // Duration
                            $start = Carbon::parse($range[$start_index]->state_time);
                            $end = Carbon::parse($range[$end_index]->state_time);

                            $range[$start_index]->duration = $start->diff($end);

                            // set end_time of equip check to the last end_time of state
                            $range[$start_index]->state_time = $range[$end_index]->state_time;

                            // Convert State
                            // $range[$start_index]->state = $this->convertState($range[$start_index]->state);

                            // push the range in table
                            array_push($equips_range_of_states, $range[$start_index]);

                            // reset the start_index var
                            $start_index = $i + 1;
                        }
                    } else {
                        if ($range[$i]->state == $range[$i - 1]->state) {

                            // Duration
                            $start = Carbon::parse($range[$start_index]->state_time);
                            $end = Carbon::parse($range[$i]->state_time);

                            $range[$start_index]->duration = $start->diff($end);

                            // set end_time of equip check to the last end_time of state
                            $range[$start_index]->state_time = $range[$i]->state_time;

                            // Convert State
                            //$range[$start_index]->state = $this->convertState($range[$start_index]->state);

                            // push the range in table
                            array_push($equips_range_of_states, $range[$start_index]);
                        } else {
                            /**** BEFOR LAST INDEX */

                            // Duration
                            $start = Carbon::parse($range[$start_index]->state_time);
                            $end = Carbon::parse($range[$i - 1]->state_time);

                            $range[$start_index]->duration = $start->diff($end);

                            // set end_time of equip check to the last end_time of state
                            $range[$start_index]->state_time = $range[$i - 1]->state_time;

                            // Convert State
                            //$range[$start_index]->state = $this->convertState($range[$start_index]->state);

                            // push the range in table
                            // array_push($equips_range_of_states, $range[$start_index]);------------------------ ¿*? ----------------------

                            /**** LAST INDEX */
                            // Convert State
                            //$range[$i]->state = $this->convertState($range[$i]->state);

                            // push the range in table
                            array_push($equips_range_of_states, $range[$i]);
                        }
                    }
                }
            }
        }

        return $equips_range_of_states;
    }

    public function OrderRanges($ranges)
    {
        return $ranges->sortByDesc('state_time');
    }

    public function EquipsNames()
    {

        if ($this->site_name == 'All') {

            return DB::table('nagios_services')
                ->join('nagios_hosts', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->select('nagios_services.display_name as equip_name', 'nagios_services.service_object_id', 'nagios_hosts.host_object_id', 'nagios_hosts.display_name as box_name')
                ->where('alias', 'box')
                ->get();
        } else {

            return DB::table('nagios_services')
                ->join('nagios_hosts', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->select('nagios_services.display_name as equip_name', 'nagios_services.service_object_id', 'nagios_hosts.host_object_id', 'nagios_hosts.display_name as box_name')
                ->where('alias', 'box')
                ->get();
        }
    }

    public function getEquipsGroups()
    {
        $equips_groups = [];
        $all_groups = [];
        $boxes = $this->getBoxes();

        $equips = EquipsNames::all();

        foreach ($boxes as $box) {

            foreach ($equips as $key => $equip) {

                if ($equip->box_name == $box->box_name) {
                    array_push($equips_groups, $equip->equip_name);
                }
            }

            array_push($all_groups, (object)['box_name' => $box->box_name, 'equips_names' => $equips_groups]);

            $equips_groups = [];
        }

        return $all_groups;
    }

    public function getAllEquipsNames()
    {

        if ($this->site_name == 'All') {

            return DB::table('nagios_services')
                ->join('nagios_hosts', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('am.equips_details as ed', 'nagios_services.display_name', '=', 'ed.pin_name')
                ->select('nagios_services.display_name as pin_name', 'ed.equip_name', 'nagios_services.service_object_id', 'nagios_hosts.host_object_id', 'nagios_hosts.display_name as box_name')
                ->where('alias', 'box')
                ->get();
        } else {

            return DB::table('nagios_services')
                ->join('nagios_hosts', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->select('nagios_services.display_name as equip_name', 'nagios_services.service_object_id', 'nagios_hosts.host_object_id', 'nagios_hosts.display_name as box_name')
                ->where('alias', 'box')
                ->get();
        }
    }

    public function getBoxes()
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

    public function paginate($items, $perPage = 25, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
   
    public function equipsCurrentState()
    {
        if ($this->site_name == 'All') {

            $current_state = DB::table('nagios_services')
                ->join('nagios_hosts', 'nagios_services.host_object_id', '=', 'nagios_hosts.host_object_id')
                ->join('am.equips_details as ed', function ($join) {
                    $join->on('nagios_services.display_name', '=', 'ed.pin_name')
                        ->on('nagios_hosts.display_name', '=', 'ed.box_name');
                })
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', 'nagios_servicestatus.service_object_id')
                ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.host_object_id', 'nagios_services.service_object_id', 'nagios_servicestatus.current_state as state', 'nagios_servicestatus.last_check as start_time', 'nagios_servicestatus.output', 'nagios_servicestatus.check_command', 'ed.equip_name', 'ed.site_name', 'ed.pin_name', 'ed.hall_name')
                ->orderBy('last_check');
        } else {

            $current_state = DB::table('nagios_services')
                ->join('nagios_hosts', 'nagios_services.host_object_id', '=', 'nagios_hosts.host_object_id')
                ->join('am.equips_details as ed', function ($join) {
                    $join->on('nagios_services.display_name', '=', 'ed.pin_name')
                        ->on('nagios_hosts.display_name', '=', 'ed.box_name');
                })
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', 'nagios_servicestatus.service_object_id')
                ->where('ed.site_name', $this->site_name)
                ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.host_object_id', 'nagios_services.service_object_id', 'nagios_servicestatus.current_state as state', 'nagios_servicestatus.last_check as start_time', 'nagios_servicestatus.output', 'nagios_servicestatus.check_command', 'ed.equip_name', 'ed.site_name', 'ed.pin_name', 'ed.hall_name')
                ->orderBy('last_check');
        }

        // filter by name
        if ($this->equip_name) {
            $current_state = $current_state->where('ed.equip_name', $this->equip_name);
        }

        // filter by input number
        if ($this->pin_nbr) {
            $current_state = $current_state->where('nagios_servicestatus.check_command', 'LIKE', 'bf1010_IN' . $this->pin_nbr . '!H%');
        }

        // filter by Date From
        if ($this->date_from) {
            $current_state = $current_state->where('nagios_servicestatus.last_check', '>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to) {
            $current_state = $current_state->where('nagios_servicestatus.last_check', '<=', date('Y-m-d', strtotime($this->date_to . ' + 1 days')));
        }

        if ($this->status != 'all') {
            $current_state = $current_state->where('nagios_servicestatus.current_state', $this->status);
        }

        return $current_state->get();

    }

    public function getBySQL()
    {

        if ($this->site_name == 'All') {

            $history = DB::table('nagios_statehistory')
                ->select('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_services.display_name as pin_name','ed.site_name', 'ed.equip_name', 'ed.box_name', 'nagios_servicestatus.check_command')
                ->selectRaw('MIN(nagios_statehistory.state_time) AS start_time')
                ->selectRaw('MAX(nagios_statehistory.state_time) AS end_time')
                ->selectRaw('TIMEDIFF(MAX(nagios_statehistory.state_time), MIN(nagios_statehistory.state_time)) AS duration')
                ->selectRaw('@group_number := @group_number + IF(@prev_state = nagios_statehistory.state, 0, 1) AS state_group')
                ->selectRaw('@prev_state := nagios_statehistory.state')
                ->join('nagios_services', 'nagios_services.service_object_id', '=', 'nagios_statehistory.object_id')
                ->join('nagios_hosts', 'nagios_services.host_object_id', '=', 'nagios_hosts.host_object_id')
                ->join('am.equips_details as ed', function ($join) {
                    $join->on('nagios_services.display_name', '=', 'ed.pin_name')
                        ->on('nagios_hosts.display_name', '=', 'ed.box_name');
                })
                ->join('nagios_servicestatus','nagios_services.service_object_id', '=', 'nagios_servicestatus.service_object_id')
                ->orderBy('nagios_statehistory.object_id')
                ->orderBy('nagios_statehistory.state_time')
                ->groupBy('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_services.display_name', 'ed.site_name', 'ed.equip_name', 'ed.box_name', 'nagios_servicestatus.check_comand');

        } else {

            $history = DB::table('nagios_statehistory')
                ->select('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_services.display_name as pin_name','ed.site_name', 'ed.equip_name', 'ed.box_name', 'nagios_servicestatus.check_command')
                ->selectRaw('MIN(nagios_statehistory.state_time) AS start_time')
                ->selectRaw('MAX(nagios_statehistory.state_time) AS end_time')
                ->selectRaw('TIMEDIFF(MAX(nagios_statehistory.state_time), MIN(nagios_statehistory.state_time)) AS duration')
                ->selectRaw('@group_number := @group_number + IF(@prev_state = nagios_statehistory.state, 0, 1) AS state_group')
                ->selectRaw('@prev_state := nagios_statehistory.state')
                ->join('nagios_services', 'nagios_services.service_object_id', '=', 'nagios_statehistory.object_id')
                ->join('nagios_hosts', 'nagios_services.host_object_id', '=', 'nagios_hosts.host_object_id')
                ->join('am.equips_details as ed', function ($join) {
                    $join->on('nagios_services.display_name', '=', 'ed.pin_name')
                        ->on('nagios_hosts.display_name', '=', 'ed.box_name');
                })
                ->join('nagios_servicestatus','nagios_services.service_object_id', '=', 'nagios_servicestatus.service_object_id')
                ->orderBy('nagios_statehistory.object_id')
                ->orderBy('nagios_statehistory.state_time')
                ->groupBy('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_services.display_name', 'ed.site_name', 'ed.equip_name', 'ed.box_name', 'nagios_servicestatus.check_command')
                ->where('ed.site_name', $this->site_name);
        }

        // filter by name
        if ($this->equip_name) {
            $history = $history->where('ed.equip_name', $this->equip_name);
        }

        // filter by input number
        if ($this->pin_nbr) {
            $history = $history->where('nagios_servicestatus.check_command', 'LIKE', 'bf1010_IN' . $this->pin_nbr . '!H%');
        }

        // filter by Date From
        if ($this->date_from) {
            $history = $history->where('nagios_statehistory.state_time', '>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to) {
            $history = $history->where('nagios_statehistory.state_time', '<=', date('Y-m-d', strtotime($this->date_to . ' + 1 days')));
        }

        if ($this->status != 'all') {
            $history = $history->where('nagios_statehistory.state', $this->status);
        }

        $history = $history->get();

        // Get Current State
        $current_state = $this->equipsCurrentState();

        // Add Current state to the historical data
        foreach ($current_state as $element) {
            
            // Get the last state of the element from statehistory table
            $last_state = $this->getStateHistory($element->service_object_id);

            // if the element has a historical data
            if ($last_state) {
                
                // if the current state is like the last historical state of the element
                if ($element->state == $last_state->state) {
                    // Last historical state
                    $last_historcal_state = $history->where('object_id', $element->service_object_id)->first();

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
                    $last_historcal_state = $history->where('object_id', $element->service_object_id)->first();

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
                $first_check = $this->getTheFirstCheck($element->service_object_id);

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

    public function getStateHistory($object_id) {
        return DB::table('nagios_statehistory')
            ->where('object_id', $object_id)
            ->select('state','state_time')
            ->orderByDesc('state_time')
            ->first();
    }

    public function getTheFirstCheck($service_object_id) {
        return DB::table('nagios_servicechecks')
            ->where('service_object_id', $service_object_id)
            ->select('state','start_time')
            ->orderBy('start_time')
            ->first();
    }
}
