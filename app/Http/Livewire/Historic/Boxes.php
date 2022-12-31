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

        // filter by Name
        if ($this->box_name)
        {
            foreach ($boxes_histories as $key => $box) {
                if ($box->box_name == $this->box_name) {
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
                $checks_of_box[0]->state = $this->convertState($checks_of_box[0]->state);
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
                            $checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

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
                            $checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]);

                        } else {
                            /**** BEFOR LAST INDEX */
                            // set end_time of equip check to the last end_time of state
                            $checks_of_box[$start_index]->end_time = $checks_of_box[$i-1]->end_time;
                            
                            // Convert State
                            $checks_of_box[$start_index]->state = $this->convertState($checks_of_box[$start_index]->state);

                            // push the range in table
                            array_push($boxes_range_of_states, $checks_of_box[$start_index]);

                            /**** LAST INDEX */
                            // Convert State
                            $checks_of_box[$i]->state = $this->convertState($checks_of_box[$i]->state);

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
                ->where('is_raw_check','=', 0);
                
        } else {

            $boxes_histories = DB::table('nagios_hostchecks')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('alias','box')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                ->where('is_raw_check','=', 0);

        }   

        // filter by Date From
        if ($this->date_from)
        {
            $boxes_histories = $boxes_histories->where('nagios_hostchecks.start_time','>=',$this->date_from);
        }

        // filter by Date To
        if ($this->date_to)
        {
            $boxes_histories = $boxes_histories->where('nagios_hostchecks.end_time','<=',$this->date_to);
        }

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



































    // public $status = 'all';
    // public $box_name;
    // public $date_from;
    // public $date_to;

    // // protected $queryString = ['status','box_name','date_from','date_to'];

    // public function render()
    // {
    //     $boxes_names = $this->getBoxesName()->get();
        
    //     $boxes_histories = [];

    //     foreach ($boxes_names as $box) {

    //         $all_boxes_checks = $this->getBoxesChecks()->where('nagios_hostchecks.host_object_id','=',$box->host_object_id)->get();

    //         if(sizeof($all_boxes_checks))
    //         {
    //             $status = $this->getStatus($all_boxes_checks);  

    //             for ($i=0; $i < sizeof($status); $i++) {
                    
    //                 $box_checks = $this->getBoxesChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][0])->get();
                    
    //                 $end_host_checks = $this->getBoxesChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][1])->get();

    //                 $box_checks[0]->end_time = $end_host_checks[0]->end_time;

    //                 array_push($boxes_histories,$box_checks[0]);
    //             }

    //         } else {
                            
    //             continue;
    //         }

    //     }

    //     if($this->status != 'all')
    //     {
    //         $boxes_histories = $this->filterByStatus($boxes_histories,$this->status);
    //     }
            
    //     if ($this->box_name) 
    //     {
    //         $boxes_histories = $this->filterByName($boxes_histories,$this->box_name);
    //     }

    //     foreach ($boxes_histories as $boxes) {

    //         unset($boxes->host_object_id);
    //         unset($boxes->hostcheck_id);

    //         switch ($boxes->state) {
    //             case 0:
    //                 $boxes->state = 'Up';
    //                 break;
    //             case 1:
    //                 $boxes->state = 'Down';
    //                 break;
    //             case 2:
    //                 $boxes->state = 'Unreachable';
    //                 break;
    //         }
    //     }
        
    //     return view('livewire.historic.boxes')
    //         ->with(['boxes_histories' => $boxes_histories,'boxes_names' => $boxes_names])
    //         ->extends('layouts.app')
    //         ->section('content');
    // }

    // public function getBoxesChecks()
    // {
    //     $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

    //     $from_date = $this->date_from;
    //     $to_date = $this->date_to;
        
    //     // Check from date
    //     if(empty($this->date_from))
    //     {
    //         $from_date = date('Y-m-d H:i:s', strtotime('-24 hours', time()));

    //     } else {
    //         $from_date = date('Y-m-d H:i:s', strtotime($from_date));
    //     }
        
    //     // Check to date
    //     if(empty($to_date))
    //     {
    //         if(empty($this->date_from))
    //         {
    //             $to_date = date('Y-m-d H:i:s');
                
    //         } else {
    //             $to_date = date('Y-m-d H:i:s', strtotime($from_date.'+24 hours'));
    //         }

    //     } else {
    //         if(empty($this->date_from))
    //         {
    //             $to_date = date('Y-m-d H:i:s', strtotime($to_date.'+22 hours'));
    //             $from_date = date('Y-m-d H:i:s', strtotime($to_date.'-24 hours'));
    //         } else {
    //             // TEMPRORY SOLUTION
    //             $to_date = date('Y-m-d H:i:s', strtotime($to_date.'+22 hours'));
    //             $from_date = date('Y-m-d H:i:s', strtotime($to_date.'-24 hours'));
    //         }
    //     }
        
    //     if ($site_name == 'All') {

    //         return DB::table('nagios_hostchecks')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
    //             ->where('alias','box')
    //             ->select('nagios_hosts.display_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
    //             ->where('is_raw_check','=', 0)
    //             ->where('nagios_hostchecks.end_time','>=',$from_date)
    //             ->where('nagios_hostchecks.end_time','<=',$to_date);
    //     } else {

    //         return DB::table('nagios_hostchecks')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->where('alias','box')
    //             ->where('nagios_customvariables.varvalue',$site_name)
    //             ->select('nagios_hosts.display_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
    //             ->where('is_raw_check','=', 0)
    //             ->where('nagios_hostchecks.end_time','>=',$from_date)
    //             ->where('nagios_hostchecks.end_time','<=',$to_date);
    //     }
        
    // }

    // public function getBoxesName()
    // {
    //     $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

    //     if ($site_name == 'All') {
            
    //         return DB::table('nagios_hosts')
    //             ->where('alias','box')
    //             ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
    //             ->orderBy('display_name');
    //     }
    //     else
    //     {
    //         return DB::table('nagios_hosts')
    //             ->where('alias','box')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->where('nagios_customvariables.varvalue',$site_name)
    //             ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
    //             ->orderBy('display_name');
    //     }
    // }

    // public function getStatus($box)
    // {
    //     $status = [];

    //     $interval = [];

    //     for ($i=0; $i < sizeof($box); $i++) { 
                
    //         if($i == 0)
    //         {
    //             array_push($interval,$box[0]->hostcheck_id);
    //         }

    //         if ($i > 0 && $i < sizeof($box)-1) {
                
    //             if($box[$i]->state == $box[$i-1]->state)
    //             {
    //                 continue;

    //             } else {

    //                 array_push($interval,$box[$i-1]->hostcheck_id);

    //                 array_push($status,$interval);

    //                 $interval = [];

    //                 array_push($interval,$box[$i]->hostcheck_id);

    //             }

    //         }

    //         if($i == sizeof($box)-1)
    //         {
    //             if($box[$i]->state == $box[$i-1]->state)
    //             {
    //                 array_push($interval,$box[$i]->hostcheck_id);
    //                 array_push($status,$interval);

    //             } else {

    //                 array_push($interval,$box[$i-1]->hostcheck_id);
    //                 array_push($status,$interval);

    //                 $interval = [];

    //                 array_push($interval,$box[$i]->hostcheck_id);
    //                 array_push($interval,$box[$i]->hostcheck_id);
    //                 array_push($status,$interval);
    //             }
    //         }

    //     }

    //     return $status;
    // }

    // public function filterByStatus($boxes_histories,$status)
    // {
    //     $boxes_filtred = [];

    //     switch ($status) {
    //         case 'up':
    //             $status = 0;
    //             break;
    //         case 'down':
    //             $status = 1;
    //             break;
    //         case 'unreachable':
    //             $status = 2;
    //             break;
    //     }

    //     foreach ($boxes_histories as $box) {
            
    //         if($box->state == $status)
    //         {
    //             array_push($boxes_filtred,$box);
    //         }

    //     }

    //     return $boxes_filtred;
    // }

    // public function filterByName($boxes_histories,$name)
    // {
    //     $hosts_filtred = [];

    //     foreach ($boxes_histories as $host) {
            
    //         if($host->display_name == $name)
    //         {
    //             array_push($hosts_filtred,$host);
    //         }

    //     }

    //     return $hosts_filtred;
    // }

    // // public function filterByDateFrom($boxes_histories,$date_from)
    // // {
    // //     $hosts_filtred = [];

    // //     foreach ($boxes_histories as $box) {
            
    // //         if($box->start_time >= $this->date_from)
    // //         {
    // //             array_push($boxes_filtred,$box);
    // //         }

    // //     }
    
    // //     return $boxes_filtred;
    // // }

    // // public function filterByDateTo($boxes_histories,$date_to)
    // // {
    // //     $boxes_filtred = [];

    // //     foreach ($boxes_histories as $box) {
            
    // //         if($box->end_time <= $this->date_to)
    // //         {
    // //             array_push($boxes_filtred,$box);
    // //         }

    // //     }
    
    // //     return $boxes_filtred;
    // // }
}
