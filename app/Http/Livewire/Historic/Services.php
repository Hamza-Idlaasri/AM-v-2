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

class Services extends Component
{
    use WithPagination;

    public $status = 'all';
    public $service_name;
    public $date_from;
    public $date_to;
    public $site_name;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        $services_histories = $this->getBySQL();

        return view('livewire.historic.services')
            ->with(['services_histories' => $this->paginate($services_histories), 'services_names' => $this->getServicesGroups(), 'download' => $services_histories])
            ->extends('layouts.app')
            ->section('content');
    }

    public function OrganizeStates($services_ranges)
    {
        $services_range_of_states = [];

        foreach ($services_ranges as $service) {

            // Get a single equipement checks
            $checks_of_service = $service;

            $start_index = 0;
            $end_index = 0;

            if (sizeof($checks_of_service) == 1) {
                // Convert State
                //$checks_of_service[0]->state = $this->convertState($checks_of_service[0]->state);
                // push the range in table
                array_push($services_range_of_states, $checks_of_service[0]);
            } else {
                // Search on single equipements checks ranges
                for ($i = 0; $i < sizeof($checks_of_service); $i++) {

                    if ($i < (sizeof($checks_of_service) - 1)) {

                        if ($checks_of_service[$i]->state == $checks_of_service[$i + 1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set state_time of service check to the last state_time of state
                            $checks_of_service[$start_index]->state_time = $checks_of_service[$end_index]->state_time;

                            // Convert State
                            //$checks_of_service[$start_index]->state = $this->convertState($checks_of_service[$start_index]->state);

                            // push the range in table
                            array_push($services_range_of_states, $checks_of_service[$start_index]);

                            // reset the start_index var
                            $start_index = $i + 1;
                        }
                    } else {
                        if ($checks_of_service[$i]->state == $checks_of_service[$i - 1]->state) {

                            // set state_time of service check to the last state_time of state
                            $checks_of_service[$start_index]->state_time = $checks_of_service[$i]->state_time;

                            // Convert State
                            //$checks_of_service[$start_index]->state = $this->convertState($checks_of_service[$start_index]->state);

                            // push the range in table
                            array_push($services_range_of_states, $checks_of_service[$start_index]);
                        } else {
                            /**** BEFOR LAST INDEX */
                            // set state_time of service check to the last state_time of state
                            $checks_of_service[$start_index]->state_time = $checks_of_service[$i - 1]->state_time;

                            // Convert State
                            //$checks_of_service[$start_index]->state = $this->convertState($checks_of_service[$start_index]->state);

                            // push the range in table
                            // array_push($services_range_of_states, $checks_of_service[$start_index]);

                            /**** LAST INDEX */
                            // Convert State
                            //$checks_of_service[$i]->state = $this->convertState($checks_of_service[$i]->state);

                            // push the range in table
                            array_push($services_range_of_states, $checks_of_service[$i]);
                        }
                    }
                }
            }
        }

        return $services_range_of_states;
    }

    public function OrderRanges($ranges)
    {
        return $ranges->sortByDesc('state_time');
    }

    public function getServicesNames()
    {

        if ($this->site_name == 'All') {

            return DB::table('nagios_services')
                ->join('nagios_hosts', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->select('nagios_services.display_name as service_name', 'nagios_services.service_object_id', 'nagios_hosts.host_object_id', 'nagios_hosts.display_name as host_name')
                ->where('alias', 'host')
                ->get();
        } else {

            return DB::table('nagios_services')
                ->join('nagios_hosts', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->select('nagios_services.display_name as service_name', 'nagios_services.service_object_id', 'nagios_hosts.host_object_id', 'nagios_hosts.display_name as host_name')
                ->where('alias', 'host')
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

    public function paginate($items, $perPage = 25, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function getHostsNames()
    {

        if ($this->site_name == 'All') {

            return DB::table('nagios_hosts')
                ->where('alias', 'host')
                ->select('nagios_hosts.display_name as host_name', 'nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();
        } else {

            return DB::table('nagios_hosts')
                ->where('alias', 'host')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->select('nagios_hosts.display_name as host_name', 'nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();
        }
    }

    public function servicesCurrentState()
    {
        if ($this->site_name == 'All') {

            $current_state = DB::table('nagios_hosts')
                ->where('alias', 'host')
                ->join('nagios_services', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', '=', 'nagios_servicestatus.service_object_id')
                ->select('nagios_hosts.display_name as host_name', 'nagios_hosts.host_object_id', 'nagios_services.display_name as service_name', 'nagios_services.service_object_id', 'nagios_servicestatus.current_state as state', 'nagios_servicestatus.last_check as start_time', 'nagios_servicestatus.output')
                ->orderBy('last_check');
        } else {
            $current_state = DB::table('nagios_hosts')
                ->where('alias', 'host')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->join('nagios_services', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', '=', 'nagios_servicestatus.service_object_id')
                ->select('nagios_hosts.display_name as host_name', 'nagios_hosts.host_object_id', 'nagios_services.display_name as service_name', 'nagios_services.service_object_id', 'nagios_servicestatus.current_state as state', 'nagios_servicestatus.last_check as start_time', 'nagios_servicestatus.output')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->orderBy('last_check');
        }

        // // filter by name
        // if ($this->service_name) {
        //     $current_state = $current_state->where('nagios_services.display_name', $this->service_name);
        // }

        // // filter by Date From
        // if ($this->date_from) {
        //     $current_state = $current_state->where('nagios_servicestatus.last_check', '>=', $this->date_from);
        // }

        // // filter by Date To
        // if ($this->date_to) {
        //     $current_state = $current_state->where('nagios_servicestatus.last_check', '<=', date('Y-m-d', strtotime($this->date_to . ' + 1 days')));
        // }

        // // filter by status
        // if ($this->status != 'all') {
        //     $current_state = $current_state->where('nagios_servicestatus.current_state', $this->status);
        // }

        return $current_state->get();
    }

    public function getBySQL()
    {
        if ($this->site_name == 'All') {

            $history = DB::table(DB::raw($this->Query()))
                ->select([
                    'nagios_services.service_object_id',
                    'nagios_statehistory.state',
                    DB::raw('MIN(nagios_statehistory.output) AS output'),
                    DB::raw('MIN(nagios_statehistory.state_time) AS start_time'),
                    DB::raw('MAX(nagios_statehistory.state_time) AS end_time'),
                    DB::raw('TIMEDIFF(MAX(nagios_statehistory.state_time), MIN(nagios_statehistory.state_time)) AS duration'),
                    'nagios_services.display_name AS service_name',
                    'nagios_hosts.display_name AS host_name',
                ])
                ->join('nagios_services', 'nagios_statehistory.object_id', '=', 'nagios_services.service_object_id')
                ->join('nagios_hosts', 'nagios_services.host_object_id', '=', 'nagios_hosts.host_object_id')
                ->groupBy([
                    'nagios_services.service_object_id',
                    'nagios_statehistory.state',
                    'nagios_statehistory.state_group',
                    'nagios_services.display_name',
                    'nagios_hosts.display_name'
                ])
                ->orderBy('nagios_services.service_object_id')
                ->orderByDesc('start_time')
                ->where('nagios_hosts.alias','host')
                ->get();
        } else {

            $history = DB::table(DB::raw($this->Query()))
                ->select([
                    'nagios_services.service_object_id',
                    'nagios_statehistory.state',
                    DB::raw('MIN(nagios_statehistory.output) AS output'),
                    DB::raw('MIN(nagios_statehistory.state_time) AS start_time'),
                    DB::raw('MAX(nagios_statehistory.state_time) AS end_time'),
                    DB::raw('TIMEDIFF(MAX(nagios_statehistory.state_time), MIN(nagios_statehistory.state_time)) AS duration'),
                    'nagios_services.display_name AS service_name',
                    'nagios_hosts.display_name AS host_name',
                ])
                ->join('nagios_services', 'nagios_statehistory.object_id', '=', 'nagios_services.service_object_id')
                ->join('nagios_hosts', 'nagios_services.host_object_id', '=', 'nagios_hosts.host_object_id')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->groupBy([
                    'nagios_services.service_object_id',
                    'nagios_statehistory.state',
                    'nagios_statehistory.state_group',
                    'nagios_services.display_name',
                    'nagios_hosts.display_name'
                ])
                ->orderBy('nagios_services.service_object_id')
                ->orderByDesc('start_time')
                ->where('nagios_hosts.alias','host')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->get();
        }

        // Get Current State
        $current_state = $this->servicesCurrentState();

        // Add Current state to the historical data
        foreach ($current_state as $element) {

            // Get the last state of the element from statehistory table
            $last_historical_state = $history->where('service_object_id', $element->service_object_id)->first();

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
                    $last_historical_state->duration = $this->getDuration($duration);
                } else {

                    // Give the end_time to the current_state
                    $element->end_time = $element->start_time;

                    // Give the start_time of the current_state the end_time of the historical state
                    $element->start_time = $last_historical_state->end_time;

                    // set the start and end time
                    $start_time = Carbon::parse($element->start_time);
                    $end_time = Carbon::parse($element->end_time);

                    // Calculate the duration
                    $duration = $start_time->diff($end_time);

                    $element->duration = $this->getDuration($duration);

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

                // set the start and end time
                $start_time = Carbon::parse($element->start_time);
                $end_time = Carbon::parse($element->end_time);

                // Calculate the duration
                $duration = $start_time->diff($end_time);

                // Add duration 
                $element->duration = $this->getDuration($duration);

                $history->prepend($element);
            }
        }

        // filter by name
        if ($this->service_name) {
            $history = $history->where('service_name', $this->service_name);
        }

        // filter by Date From
        if ($this->date_from) {
            $history = $history->where('start_time', '>=', $this->date_from);
        }

        // filter by Date To
        if ($this->date_to) {
            $history = $history->where('end_time', '<=', date('Y-m-d', strtotime($this->date_to . ' + 1 days')));
        }

        // filter by status
        if ($this->status != 'all') {
            $history = $history->where('state', $this->status);
        }

        return $history;
    }

    public function getTheFirstCheck($service_object_id)
    {
        return DB::table('nagios_servicechecks')
            ->where('service_object_id', $service_object_id)
            ->select('state', 'start_time')
            ->orderBy('start_time')
            ->first();
    }

    public function getDuration($duration)
    {
        $h = ($duration->h) + ($duration->d * 24) + ($duration->m * 30 * 24) + ($duration->y * 365 * 24);

        return $h . ':' . $duration->i . ':' . $duration->s;
    }

    public function Query() {
        return "(SELECT
                object_id,
                state,
                state_time,
                output,
                @group := IF(@prevState = state, @group, @group + 1) AS state_group,
                @prevState := state
            FROM
                nagios_statehistory,
                (SELECT @group := 0, @prevState := null) AS vars
            ORDER BY
                object_id,
                state_time) AS nagios_statehistory
        ";
    }
}
