<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $boxes_histories = $this->getStateRanges();
        // dd($this->getHistory());
        // filter by state
        if($this->status != 'all')
        {
            foreach ($boxes_histories as $key => $box) {
                if ($box->state == $this->status) {
                    continue;
                } else {
                    unset($boxes_histories[$key]);
                }
            }

            $boxes_histories = array_values($boxes_histories);

        }    

        return view('livewire.historic.boxes')
            ->with(['boxes_histories' => $this->paginate($boxes_histories), 'boxes_names' => $this->getBoxes(),'download' => $boxes_histories])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getStateRanges()
    {
        $boxes_names = $this->getBoxes();

        $boxes_ranges = [];

        foreach ($boxes_names as $equip) {

            $checks = $this->getBoxesChecks()->where('nagios_hosts.host_object_id', $equip->host_object_id)->get();

            if(!empty($checks)) {
                array_push($boxes_ranges, $checks);
            }

            unset($checks);
        }
        
        return $this->OrganizeStates($boxes_ranges);
    }

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
                // Search on single equipements checks ranges
                for ($i=0; $i < sizeof($checks_of_box); $i++) {
                    
                    if ($i < (sizeof($checks_of_box)-1)) {

                        if ($checks_of_box[$i]->state == $checks_of_box[$i+1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set end_time of equip check to the last end_time of state
                            $checks_of_box[$start_index]->end_time = $checks_of_box[$end_index]->end_time;

                            // Convert State
                            //$checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]);

                            // reset the start_index var
                            $start_index = $i+1;
                        }

                    } else {
                        if ($checks_of_box[$i]->state == $checks_of_box[$i-1]->state) {

                            // set end_time of equip check to the last end_time of state
                            $checks_of_box[$start_index]->end_time = $checks_of_box[$i]->end_time;
                            
                            // Convert State
                            //$checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]);

                        } else {
                            /**** BEFOR LAST INDEX */
                            // set end_time of equip check to the last end_time of state
                            $checks_of_box[$start_index]->end_time = $checks_of_box[$i-1]->end_time;
                            
                            // Convert State
                            //$checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]);

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

        return $this->OrderRanges($boxes_range_of_states);
    }

    public function OrderRanges($ranges)
    {
        usort($ranges, function ($item1, $item2) {
            return $item2->hostcheck_id <=> $item1->hostcheck_id;
        });    
        
        return $ranges;
    }

    public function getBoxesChecks()
    {
        
        if ($this->site_name == 'All') {
            
            $boxes_histories = DB::table('nagios_hostchecks')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
                ->where('alias','box')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                ->where('is_raw_check','=', 0)
                ->orderBy('nagios_hostchecks.start_time');
                
        } else {

            $boxes_histories = DB::table('nagios_hostchecks')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('alias','box')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                ->where('is_raw_check','=', 0)
                ->orderBy('nagios_hostchecks.start_time');
                
        }   

        // filter by name
        if ($this->box_name) {
            $boxes_histories = $boxes_histories->where('nagios_hosts.display_name',$this->box_name);
        }

        // filter by Date From
        if ($this->date_from)
        {
            $boxes_histories = $boxes_histories->where('nagios_hostchecks.start_time','>=',$this->date_from);
        }

        // filter by Date To
        if ($this->date_to)
        {
            $boxes_histories = $boxes_histories->where('nagios_hostchecks.start_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
        }

        $boxes_histories = $boxes_histories->take(20000);

        return $boxes_histories;
    }

    public function getBoxes()
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

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    // public function convertState($state)
    // {
    //     switch ($state) {
    //         case 0:
    //             return  $state = 'Up';
    //             break;
    //         case 1:
    //             return  $state = 'Down';
    //             break;
    //         case 2:
    //             return  $state = 'Unreachable';
    //             break;
    //     }
    // }

    public function getHistory()
    {
        if ($this->site_name == "All") {
            
            return DB::table('nagios_statehistory')
                ->join('nagios_hosts','nagios_statehistory.object_id','=','nagios_hosts.host_object_id')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_hosts.address','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.output')
                ->where('alias','box')
                ->orderBy('nagios_statehistory.state_time')
                ->get();

        } else {

            return DB::table('nagios_statehistory')
                ->join('nagios_hosts','nagios_statehistory.object_id','=','nagios_hosts.host_object_id') 
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_hosts.address','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.output')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->orderBy('nagios_statehistory.state_time')
                ->get();
        }
        
    }
}