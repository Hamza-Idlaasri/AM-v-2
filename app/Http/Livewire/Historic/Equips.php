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

class Equips extends Component
{
    use WithPagination;

    public $status = 'all';
    public $equip_name;
    public $date_from;
    public $date_to;
    public $site_name;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;
        
        $equips_histories = $this->getHistory();

        // filter by state
        if($this->status != 'all')
        {
            foreach ($equips_histories as $key => $equip) {
                if ($equip->state == $this->status) {
                    continue;
                } else {
                    $equips_histories->forget($key);
                }
            }

        }    

        return view('livewire.historic.equips')
            ->with(['equips_histories' => $this->paginate($equips_histories), 'equips_names' => $this->getEquipsGroups(), 'download' => $equips_histories])
            ->extends('layouts.app')
            ->section('content');
    }

    // public function getStateRanges()
    // {
    //     $equips_names = $this->EquipsNames();

    //     $equips_ranges = [];

    //     foreach ($equips_names as $equip) {

    //         $checks = $this->getEquipsChecks()->where('nagios_services.service_object_id', $equip->service_object_id)->get();

    //         if(!empty($checks)) {
    //             array_push($equips_ranges, $checks);
    //         }

    //         unset($checks);
    //     }
        
    //     return $this->OrganizeStates($equips_ranges);
    // }

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

                        if ($range[$i]->state == $range[$i+1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set end_time of equip check to the last end_time of state
                            $range[$start_index]->state_time = $range[$end_index]->state_time;

                            // Convert State
                            //$range[$start_index]->state = $this->convertState($range[$start_index]->state);

                            // push the range in table
                            array_push($equips_range_of_states, $range[$start_index]);

                            // reset the start_index var
                            $start_index = $i+1;
                        }

                    } else {
                        if ($range[$i]->state == $range[$i-1]->state) {

                            // set end_time of equip check to the last end_time of state
                            $range[$start_index]->state_time = $range[$i]->state_time;
                            
                            // Convert State
                            //$range[$start_index]->state = $this->convertState($range[$start_index]->state);

                            // push the range in table
                            array_push($equips_range_of_states, $range[$start_index]);

                        } else {
                            /**** BEFOR LAST INDEX */
                            // set end_time of equip check to the last end_time of state
                            $range[$start_index]->state_time = $range[$i-1]->state_time;
                            
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

    // public function getEquipsChecks()
    // {
        
    //     if ($this->site_name == 'All') {
            
    //         $equips_histories = DB::table('nagios_servicechecks')
    //             ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //             ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
    //             ->where('alias','box')
    //             ->orderBy('nagios_servicechecks.start_time');

    //     } else {

    //         $equips_histories = DB::table('nagios_servicechecks')
    //             ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->where('nagios_customvariables.varvalue',$this->site_name)
    //             ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
    //             ->where('alias','box')
    //             ->orderBy('nagios_servicechecks.start_time');

    //     }
        
    //     // filter by name
    //     if ($this->equip_name) {
    //         $equips_histories = $equips_histories->where('nagios_services.display_name',$this->equip_name);
    //     }

    //     // filter by Date From
    //     if ($this->date_from)
    //     {
    //         $equips_histories = $equips_histories->where('nagios_servicechecks.start_time','>=',$this->date_from);
    //     }

    //     // filter by Date To
    //     if ($this->date_to)
    //     {
    //         $equips_histories = $equips_histories->where('nagios_servicechecks.start_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
    //     }

    //     $equips_histories = $equips_histories->take(20000);

    //     return $equips_histories;
    // }

    public function EquipsNames()
    {
        
        if ($this->site_name == 'All') {

            return DB::table('nagios_services')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_hosts.host_object_id','nagios_hosts.display_name as box_name')
                ->where('alias','box')
                ->get();

        } else {
        
            return DB::table('nagios_services')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_hosts.host_object_id','nagios_hosts.display_name as box_name')
                ->where('alias','box')
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
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('am.equips_details as ed','nagios_services.display_name','=','ed.pin_name')
                ->select('nagios_services.display_name as pin_name','ed.equip_name','nagios_services.service_object_id','nagios_hosts.host_object_id','nagios_hosts.display_name as box_name')
                ->where('alias','box')
                ->get();

        } else {
        
            return DB::table('nagios_services')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_hosts.host_object_id','nagios_hosts.display_name as box_name')
                ->where('alias','box')
                ->get();

        }
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

    public function paginate($items, $perPage = 50, $page = null, $options = [])
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
                ->join('nagios_services','nagios_statehistory.object_id','=','nagios_services.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('am.equips_details as ed','nagios_services.display_name','=','ed.pin_name')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as pin_name','ed.equip_name','ed.site_name','nagios_services.service_object_id','nagios_statehistory.statehistory_id','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.state_time_usec','nagios_statehistory.output')
                ->where('alias','box')
                ->orderBy('nagios_statehistory.state_time');

        } else {
            
            $history = DB::table('nagios_statehistory')
                ->join('nagios_services','nagios_statehistory.object_id','=','nagios_services.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('am.equips_details as ed','nagios_services.display_name','=','ed.pin_name')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as pin_name','ed.equip_name','ed.site_name','nagios_services.service_object_id','nagios_statehistory.statehistory_id','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.state_time_usec','nagios_statehistory.output')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->orderBy('nagios_statehistory.state_time');
                
        }

        // filter by name
        if ($this->equip_name) {
            $history = $history->where('ed.equip_name', $this->equip_name);
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

        $history = $history->chunk(1000, function ($equips_history) use (&$collection) {

                    $equips_names = $this->EquipsNames();

                    $equips_ranges = [];

                    foreach ($equips_names as $equip) {

                        $checks = [];

                        foreach ($equips_history as $history) {
                            if ($history->service_object_id == $equip->service_object_id) {
                                array_push($checks, $history);
                            }
                        }

                        if(!empty($checks)) {
                            array_push($equips_ranges, $checks);
                        }

                        unset($checks);
                    }
                    
                    
                    $ranges = $this->OrganizeStates($equips_ranges);

                    foreach ($ranges as $range) {
                        $collection->push($range);
                    }

                });
    
        return $this->OrderRanges($collection);

    }
}