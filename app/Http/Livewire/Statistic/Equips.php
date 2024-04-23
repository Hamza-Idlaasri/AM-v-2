<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use App\Models\EquipsNames;
use Carbon\Carbon;

class Equips extends Component
{
    // Site Name
    public $site_name;

    // Filter
    public $equip_name;
    public $pin_nbr;
    public $date_from;
    public $date_to;

    // Statistics
    public $equips_ok = 0;
    public $equips_warning = 0;
    public $equips_critical = 0;
    public $equips_unknown = 0;

    public $equips_status;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        // $this->getHistory();
        $this->getBySQL();

        $this->equips_status = [$this->equips_ok, $this->equips_warning, $this->equips_critical, $this->equips_unknown];


        return view('livewire.statistic.equips')
            ->with(['equips_status' => $this->equips_status, 'equips_names' => $this->getEquipsGroups()])
            ->extends('layouts.app')
            ->section('content');
    }

    public function OrganizeStates($equips_ranges)
    {
        $equips_range_of_states = [];

        foreach ($equips_ranges as $equip) {

            // Get a single equipement checks
            $checks_of_equip = $equip;

            $start_index = 0;
            $end_index = 0;

            if (sizeof($checks_of_equip) == 1) {
                // push the state in table
                array_push($equips_range_of_states, $checks_of_equip[0]->state);
            } else {
                // Search on single equipements checks ranges
                for ($i = 0; $i < sizeof($checks_of_equip); $i++) {

                    if ($i < (sizeof($checks_of_equip) - 1)) {

                        if ($checks_of_equip[$i]->state == $checks_of_equip[$i + 1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set end_time of equip check to the last end_time of state
                            // $checks_of_equip[$start_index]->end_time = $checks_of_equip[$end_index]->end_time;

                            // push the state in table
                            array_push($equips_range_of_states, $checks_of_equip[$start_index]->state);

                            // reset the start_index var
                            $start_index = $i + 1;
                        }
                    } else {
                        if ($checks_of_equip[$i]->state == $checks_of_equip[$i - 1]->state) {

                            // set end_time of equip check to the last end_time of state
                            // $checks_of_equip[$start_index]->end_time = $checks_of_equip[$i]->end_time;

                            // push the state in table
                            array_push($equips_range_of_states, $checks_of_equip[$start_index]->state);
                        } else {
                            /**** BEFOR LAST INDEX */
                            // set end_time of equip check to the last end_time of state
                            // $checks_of_equip[$start_index]->end_time = $checks_of_equip[$i-1]->end_time;

                            // push the state in table
                            array_push($equips_range_of_states, $checks_of_equip[$start_index]->state);

                            /**** LAST INDEX */
                            // push the state in table
                            array_push($equips_range_of_states, $checks_of_equip[$i]->state);
                        }
                    }
                }
            }
        }

        return $equips_range_of_states;
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

    // Last Update
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
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', 'nagios_servicestatus.service_object_id')
                ->orderBy('nagios_statehistory.object_id')
                ->orderByDesc('nagios_statehistory.state_time')
                ->groupBy('nagios_statehistory.object_id', 'nagios_statehistory.state', 'nagios_services.display_name', 'ed.site_name', 'ed.equip_name', 'ed.box_name', 'nagios_servicestatus.check_command');

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
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', 'nagios_servicestatus.service_object_id')
                ->orderBy('nagios_statehistory.object_id')
                ->orderByDesc('nagios_statehistory.state_time')
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

        $history = $history->get();

        // Get Current State
        $current_state = $this->equipsCurrentState();

        // Add Current state to the historical data
        foreach ($current_state as $element) {
            
            // Get the last state of the element from statehistory table
            $last_historical_state = $history->where('object_id', $element->service_object_id)->first();

            // if the element has a historical data
            if ($last_historical_state) {
                
                // if the current state is like the last historical state of the element
                if ($element->state == $last_historical_state->state) {

                    // set the start and end time
                    $start_time = Carbon::parse($last_historical_state->start_time);
                    $end_time = Carbon::parse($element->start_time);
                    
                    // Calcule duration
                    $duration = $start_time->diff($end_time);

                    // Update the end_time of the historical data
                    $last_historical_state->end_time = $element->start_time;
                    
                    // Update the duration of the last historical state
                    $last_historical_state->duration = $duration->format('%H:%i:%s');

                } else {

                    // Give the end_time to the current_state
                    $element->end_time = $element->start_time;
                    
                    // Give the start_time of the current_state the end_time of the historical state
                    $element->start_time = $last_historical_state->end_time;

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

        $this->SortStatus($history);

    }

    public function getTheFirstCheck($service_object_id) {
        return DB::table('nagios_servicechecks')
            ->where('service_object_id', $service_object_id)
            ->select('state','start_time')
            ->orderBy('start_time')
            ->first();
    }

    public function SortStatus($equips)
    {
        $this->equips_ok = 0;
        $this->equips_warning = 0;
        $this->equips_critical = 0;
        $this->equips_unknown = 0;

        foreach ($equips as $equip) {

            switch ($equip->state) {
                case 0:
                    $this->equips_ok++;
                    break;
                case 1:
                    $this->equips_warning++;
                    break;
                case 2:
                    $this->equips_critical++;
                    break;
                case 3:
                    $this->equips_unknown++;
                    break;
            }
        }
    }
}
