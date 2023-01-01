<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Hosts extends Component
{
    use WithPagination;

    public $status = 'all';
    public $host_name;
    public $date_from;
    public $date_to;
    public $site_name;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $hosts_histories = $this->getStateRanges();

        // filter by state
        if($this->status != 'all')
        {
            foreach ($hosts_histories as $key => $host) {
                if ($host->state == $this->status) {
                    continue;
                } else {
                    unset($hosts_histories[$key]);
                }
            }

            $hosts_histories = array_values($hosts_histories);

        }    

        // filter by Name
        if ($this->host_name)
        {
            foreach ($hosts_histories as $key => $host) {
                if ($host->host_name == $this->host_name) {
                    continue;
                } else {
                    unset($hosts_histories[$key]);
                }
            }

            $hosts_histories = array_values($hosts_histories);
        }

        return view('livewire.historic.hosts')
            ->with(['hosts_histories' => $this->paginate($hosts_histories), 'hosts_names' => $this->getHosts(),'download' => $hosts_histories])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getStateRanges()
    {
        $hosts_names = $this->getHosts();

        $hostes_ranges = [];

        foreach ($hosts_names as $equip) {

            $checks = $this->getHostsChecks()->where('nagios_hosts.host_object_id', $equip->host_object_id)->get();

            if(!empty($checks)) {
                array_push($hostes_ranges, $checks);
            }

            unset($checks);
        }
        
        return $this->OrganizeStates($hostes_ranges);
    }

    public function OrganizeStates($hostes_ranges)
    {
        $hostes_range_of_states = [];

        foreach ($hostes_ranges as $host) {
            
            // Get a single box checks
            $checks_of_host = $host;
            
            $start_index = 0;
            $end_index = 0;

            if (sizeof($checks_of_host) == 1) {
                // Convert State
                $checks_of_host[0]->state = $this->convertState($checks_of_host[0]->state);
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

                            // set end_time of host check to the last end_time of state
                            $checks_of_host[$start_index]->end_time = $checks_of_host[$end_index]->end_time;

                            // Convert State
                            $checks_of_host[$start_index]->state = $this->convertState($checks_of_host[$start_index]->state);

                            // push the range in table
                            array_push($hostes_range_of_states, $checks_of_host[$start_index]);

                            // reset the start_index var
                            $start_index = $i+1;
                        }

                    } else {
                        if ($checks_of_host[$i]->state == $checks_of_host[$i-1]->state) {

                            // set end_time of host check to the last end_time of state
                            $checks_of_host[$start_index]->end_time = $checks_of_host[$i]->end_time;
                            
                            // Convert State
                            $checks_of_host[$start_index]->state = $this->convertState($checks_of_host[$start_index]->state);

                            // push the range in table
                            array_push($hostes_range_of_states, $checks_of_host[$start_index]);

                        } else {
                            /**** BEFOR LAST INDEX */
                            // set end_time of host check to the last end_time of state
                            $checks_of_host[$start_index]->end_time = $checks_of_host[$i-1]->end_time;
                            
                            // Convert State
                            $checks_of_host[$start_index]->state = $this->convertState($checks_of_host[$start_index]->state);

                            // push the range in table
                            array_push($hostes_range_of_states, $checks_of_host[$start_index]);

                            /**** LAST INDEX */
                            // Convert State
                            $checks_of_host[$i]->state = $this->convertState($checks_of_host[$i]->state);

                            // push the range in table
                            array_push($hostes_range_of_states, $checks_of_host[$i]);
                        }
                    }

                }
            }
            
        }

        return $this->OrderRanges($hostes_range_of_states);
    }

    public function OrderRanges($ranges)
    {
        usort($ranges, function ($item1, $item2) {
            return $item2->hostcheck_id <=> $item1->hostcheck_id;
        });    
        
        return $ranges;
    }

    public function getHostsChecks()
    {
        
        if ($this->site_name == 'All') {
            
            $hosts_histories = DB::table('nagios_hostchecks')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
                ->where('alias','host')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                ->where('is_raw_check','=', 0)
                ->orderBy('nagios_hostchecks.start_time')
                ->take(20000);
                
        } else {

            $hosts_histories = DB::table('nagios_hostchecks')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('alias','host')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                ->where('is_raw_check','=', 0)
                ->orderBy('nagios_hostchecks.start_time')
                ->take(20000);
        }   

        // filter by Date From
        if ($this->date_from)
        {
            $hosts_histories = $hosts_histories->where('nagios_hostchecks.start_time','>=',$this->date_from);
        }

        // filter by Date To
        if ($this->date_to)
        {
            $hosts_histories = $hosts_histories->where('nagios_hostchecks.start_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
        }

        return $hosts_histories;
    }

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

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function convertState($state)
    {
        switch ($state) {
            case 0:
                return  $state = 'Up';
                break;
            case 1:
                return  $state = 'Down';
                break;
            case 2:
                return  $state = 'Unreachable';
                break;
        }
    }
}
