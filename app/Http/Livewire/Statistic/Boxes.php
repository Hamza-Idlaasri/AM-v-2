<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use Carbon\Carbon;

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

        $this->getBySQL();
        
        $this->boxes_status = [$this->boxes_up,$this->boxes_down,$this->boxes_unreachable];

        return view('livewire.statistic.boxes')
            ->with(['boxes_status' => $this->boxes_status, 'boxes_names' => $this->getBoxesNames()])
            ->extends('layouts.app')
            ->section('content');
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
                ->orderByDesc('nagios_statehistory.state_time')
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
                ->orderByDesc('nagios_statehistory.state_time')
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

        $history = $history->get();

        // Get Current State
        $current_state = $this->boxesCurrentState();

        // Add Current state to the historical data
        foreach ($current_state as $element) {

            // Get the last state of the element from statehistory table
            $last_historical_state = $history->where('object_id', $element->host_object_id)->first();

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

    public function getTheFirstCheck($host_object_id)
    {
        return DB::table('nagios_hostchecks')
            ->where('host_object_id', $host_object_id)
            ->select('state', 'start_time')
            ->orderBy('start_time')
            ->first();
    }

    public function SortStatus($boxes)
    {
        $this->boxes_up = 0;
        $this->boxes_down = 0;
        $this->boxes_unreachable = 0;
        
        foreach ($boxes as $box) {
            
            switch ($box->state) {
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
}

