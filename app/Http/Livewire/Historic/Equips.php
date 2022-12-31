<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
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
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $equips_histories = $this->getStateRanges();

        // filter by state
        if($this->status != 'all')
        {
            foreach ($equips_histories as $key => $equip) {
                if ($equip->state === $this->status) {
                    continue;
                } else {
                    unset($equips_histories[$key]);
                }
            }

            $equips_histories = array_values($equips_histories);

        }    

        // filter by Name
        if ($this->equip_name)
        {
            foreach ($equips_histories as $key => $equip) {
                if ($equip->equip_name == $this->equip_name) {
                    continue;
                } else {
                    unset($equips_histories[$key]);
                }
            }

            $equips_histories = array_values($equips_histories);
        }

        return view('livewire.historic.equips')
            ->with(['equips_histories' => $this->paginate($equips_histories), 'equips_names' => $this->getEquipsGroups(),'download' => $equips_histories])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getStateRanges()
    {
        $equips_names = $this->EquipsNames();

        $equips_ranges = [];

        foreach ($equips_names as $equip) {

            $checks = $this->getEquipsChecks()->where('nagios_services.service_object_id', $equip->service_object_id)->get();

            if(!empty($checks)) {
                array_push($equips_ranges, $checks);
            }

            unset($checks);
        }
        
        return $this->OrganizeStates($equips_ranges);
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
                // Convert State
                $checks_of_equip[0]->state = $this->convertState($checks_of_equip[0]->state);
                // push the range in table
                array_push($equips_range_of_states, $checks_of_equip[0]);
            } else {
                // Search on single equipements checks ranges
                for ($i=0; $i < sizeof($checks_of_equip); $i++) {
                    
                    if ($i < (sizeof($checks_of_equip)-1)) {

                        if ($checks_of_equip[$i]->state == $checks_of_equip[$i+1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set end_time of equip check to the last end_time of state
                            $checks_of_equip[$start_index]->end_time = $checks_of_equip[$end_index]->end_time;

                            // Convert State
                            $checks_of_equip[$start_index]->state = $this->convertState($checks_of_equip[$start_index]->state);

                            // push the range in table
                            array_push($equips_range_of_states, $checks_of_equip[$start_index]);

                            // reset the start_index var
                            $start_index = $i+1;
                        }

                    } else {
                        if ($checks_of_equip[$i]->state == $checks_of_equip[$i-1]->state) {

                            // set end_time of equip check to the last end_time of state
                            $checks_of_equip[$start_index]->end_time = $checks_of_equip[$i]->end_time;
                            
                            // Convert State
                            $checks_of_equip[$start_index]->state = $this->convertState($checks_of_equip[$start_index]->state);

                            // push the range in table
                            array_push($equips_range_of_states, $checks_of_equip[$start_index]);

                        } else {
                            /**** BEFOR LAST INDEX */
                            // set end_time of equip check to the last end_time of state
                            $checks_of_equip[$start_index]->end_time = $checks_of_equip[$i-1]->end_time;
                            
                            // Convert State
                            $checks_of_equip[$start_index]->state = $this->convertState($checks_of_equip[$start_index]->state);

                            // push the range in table
                            array_push($equips_range_of_states, $checks_of_equip[$start_index]);

                            /**** LAST INDEX */
                            // Convert State
                            $checks_of_equip[$i]->state = $this->convertState($checks_of_equip[$i]->state);

                            // push the range in table
                            array_push($equips_range_of_states, $checks_of_equip[$i]);
                        }
                    }

                }
            }
            
        }

        return $this->OrderRanges($equips_range_of_states);
    }

    public function OrderRanges($ranges)
    {
        usort($ranges, function ($item1, $item2) {
            return $item2->servicecheck_id <=> $item1->servicecheck_id;
        });    
        
        return $ranges;
    }

    public function getEquipsChecks()
    {
        
        if ($this->site_name == 'All') {
            
            $equips_histories = DB::table('nagios_servicechecks')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                ->where('alias','box');
                
        } else {

            $equips_histories = DB::table('nagios_servicechecks')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                ->where('alias','box');

        }
        
        // filter by Date From
        if ($this->date_from)
        {
            $equips_histories = $equips_histories->where('nagios_servicechecks.start_time','>=',$this->date_from);
        }

        // filter by Date To
        if ($this->date_to)
        {
            $equips_histories = $equips_histories->where('nagios_servicechecks.end_time','<=',$this->date_to);
        }

        return $equips_histories;
    }

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
        $groups = [];
        $boxes = $this->getBoxes();
        $all_groups = [];
    
        foreach ($boxes as $box) {
    
            $group = [];
    
            foreach ($this->EquipsNames() as $equip) {
    
                if($equip->host_object_id == $box->host_object_id)
                {
                    array_push($group,$equip);
                }
            }
    
            array_push($groups,$group);
        }
    
        $equips = [];
    
        for ($i=0; $i < sizeof($groups); $i++) {
    
            foreach ($groups[$i] as $gp) {
    
                array_push($equips,$gp->equip_name);
    
            }
    
            array_push($all_groups,(object)['box_name' => $groups[$i][0]->box_name, 'equips' => $equips]);
    
            $equips = [];
        }
    
        return $all_groups;
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
                return  $state = 'Ok';
                break;
            case 1:
                return  $state = 'Warning';
                break;
            case 2:
                return  $state = 'Critical';
                break;
            case 3:
                return  $state = 'Unknown';
                break;
        }
    }
}



























//     public $status = 'all';
//     public $equip_name;
//     public $date_from;
//     public $date_to;

//     public function render()
//     {
//         $equips_names = $this->getEquipsName()->get();

//         $equips_histories = $this->getEquipsChecks()
//             // ->where('nagios_servicechecks.service_object_id','=',$equip->service_object_id)
//             // ->take(3)
//             // ->get();
// ;
//         // foreach ($equips_names as $equip) {

//         //     $all_equips_checks = $this->getEquipsChecks()
//         //         ->where('nagios_servicechecks.service_object_id','=',$equip->service_object_id)
//         //         ->take(3)
//         //         ->get();

//         //     if(sizeof($all_equips_checks))
//         //     {
//         //         $status = $this->getStatus($all_equips_checks);

//         //         for ($i=0; $i < sizeof($status); $i++) {

//         //             $equip_checks = $this->getEquipsChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][0])->get();

//         //             $end_host_checks = $this->getEquipsChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][1])->get();

//         //             $equip_checks[0]->end_time = $end_host_checks[0]->end_time;

//         //             array_push($equips_histories,$equip_checks[0]);
//         //         }

//         //     } else {

//         //         continue;
//         //     }

//         // }

//         if($this->status != 'all')
//         {
//             // $equips_histories = $this->filterByStatus($equips_histories,$this->status);

//             switch ($this->status) {
//                 case 'ok':
//                     $equips_histories = $equips_histories->where('nagios_servicechecks.state','=',0);
//                     break;
//                 case 'warning':
//                     $equips_histories = $equips_histories->where('nagios_servicechecks.state','=',1);
//                     break;
//                 case 'critical':
//                     $equips_histories = $equips_histories->where('nagios_servicechecks.state','=',2);
//                     break;
//                 case 'unknown':
//                     $equips_histories = $equips_histories->where('nagios_servicechecks.state','=',3);
//                     break;
//             }
//         }

//         if ($this->equip_name)
//         {
//             $equips_histories = $equips_histories->where('nagios_services.display_name','=',$this->equip_name);
//         }

//         $equips_histories = $equips_histories->get();

//         foreach ($equips_histories as $equips) {

//             unset($equips->alias);
//             unset($equips->host_object_id);
//             unset($equips->service_object_id);
//             unset($equips->servicecheck_id);

//             switch ($equips->state) {
//                 case 0:
//                     $equips->state = 'Ok';
//                     break;
//                 case 1:
//                     $equips->state = 'Warning';
//                     break;
//                 case 2:
//                     $equips->state = 'Critical';
//                     break;
//                 case 3:
//                     $equips->state = 'Unknown';
//                     break;
//             }
//         }

//         return view('livewire.historic.equips')
//             ->with(['equips_histories' => $equips_histories, 'equips_names' => $this->getEquipsGroups($equips_names)])
//             ->extends('layouts.app')
//             ->section('content');
//     }

//     public function getEquipsChecks()
//     {
//         

//         $from_date = $this->date_from;
//         $to_date = $this->date_to;

//         // Check from date
//         if(empty($this->date_from))
//         {
//             $from_date = date('Y-m-d H:i:s', strtotime('-24 hours', time()));

//         } else {
//             $from_date = date('Y-m-d H:i:s', strtotime($from_date));
//         }

//         // Check to date
//         if(empty($to_date))
//         {
//             if(empty($this->date_from))
//             {
//                 $to_date = date('Y-m-d H:i:s');

//             } else {
//                 $to_date = date('Y-m-d H:i:s', strtotime($from_date.'+24 hours'));
//             }

//         } else {
//             if(empty($this->date_from))
//             {
//                 $to_date = date('Y-m-d H:i:s', strtotime($to_date.'+22 hours'));
//                 $from_date = date('Y-m-d H:i:s', strtotime($to_date.'-24 hours'));
//             } else {
//                 // TEMPRORY SOLUTION
//                 $to_date = date('Y-m-d H:i:s', strtotime($to_date.'+22 hours'));
//                 $from_date = date('Y-m-d H:i:s', strtotime($to_date.'-24 hours'));
//             }
//         }

//         if ($this->site_name == 'All') {

//             return DB::table('nagios_servicechecks')
//                 ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
//                 ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
//                 ->select('nagios_hosts.alias','nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
//                 ->where('alias','box')
//                 ->where('nagios_servicechecks.end_time','>=',$from_date)
//                 ->where('nagios_servicechecks.end_time','<=',$to_date);

//         } else {
//             return DB::table('nagios_servicechecks')
//                 ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
//                 ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
//                 ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
//                 ->select('nagios_hosts.alias','nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
//                 ->where('alias','box')
//                 ->where('nagios_customvariables.varvalue',$this->site_name)
//                 ->where('nagios_servicechecks.end_time','>=',$from_date)
//                 ->where('nagios_servicechecks.end_time','<=',$to_date);

//         }
//     }

//     public function getEquipsName()
//     {
//         

//         if ($this->site_name == 'All') {

//             return DB::table('nagios_hosts')
//                 ->where('alias','box')
//                 ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
//                 ->select('nagios_hosts.host_object_id','nagios_hosts.display_name as box_name','nagios_services.service_object_id','nagios_services.display_name as equip_name');
//         }
//         else
//         {
//             return DB::table('nagios_hosts')
//                 ->where('alias','box')
//                 ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
//                 ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
//                 ->where('nagios_customvariables.varvalue',$this->site_name)
//                 ->select('nagios_hosts.host_object_id','nagios_hosts.display_name as box_name','nagios_services.service_object_id','nagios_services.display_name as equip_name');
//         }

//     }

//     // public function getStatus($equip)
//     // {
//     //     $status = [];

//     //     $interval = [];

//     //     for ($i=0; $i < sizeof($equip); $i++) {

//     //         if($i == 0)
//     //         {
//     //             array_push($interval,$equip[0]->servicecheck_id);
//     //         }

//     //         if ($i > 0 && $i < sizeof($equip)-1) {

//     //             if($equip[$i]->state == $equip[$i-1]->state)
//     //             {
//     //                 continue;

//     //             } else {

//     //                 array_push($interval,$equip[$i-1]->servicecheck_id);

//     //                 array_push($status,$interval);

//     //                 $interval = [];

//     //                 array_push($interval,$equip[$i]->servicecheck_id);

//     //             }

//     //         }

//     //         if($i == sizeof($equip)-1)
//     //         {
//     //             if($equip[$i]->state == $equip[$i-1]->state)
//     //             {
//     //                 array_push($interval,$equip[$i]->servicecheck_id);
//     //                 array_push($status,$interval);

//     //             } else {

//     //                 array_push($interval,$equip[$i-1]->servicecheck_id);
//     //                 array_push($status,$interval);

//     //                 $interval = [];

//     //                 array_push($interval,$equip[$i]->servicecheck_id);
//     //                 array_push($interval,$equip[$i]->servicecheck_id);
//     //                 array_push($status,$interval);
//     //             }
//     //         }

//     //     }

//     //     return $status;
//     // }

//     // public function filterByStatus($equips_histories,$status)
//     // {
//     //     $equips_filtred = [];

//     //     switch ($status) {
//     //         case 'ok':
//     //             $status = 0;
//     //             break;
//     //         case 'warning':
//     //             $status = 1;
//     //             break;
//     //         case 'critical':
//     //             $status = 2;
//     //             break;
//     //         case 'unknown':
//     //             $status = 3;
//     //             break;
//     //     }

//     //     foreach ($equips_histories as $equip) {

//     //         if($equip->state == $status)
//     //         {
//     //             array_push($equips_filtred,$equip);
//     //         }

//     //     }

//     //     return $equips_filtred;
//     // }

//     // public function filterByName($equips_histories,$name)
//     // {
//     //     $equips_filtred = [];

//     //     foreach ($equips_histories as $equip) {

//     //         if($equip->equip_name == $name)
//     //         {
//     //             array_push($equips_filtred,$equip);
//     //         }

//     //     }

//     //     return $equips_filtred;
//     // }

//     // public function filterByDateFrom($equips_histories,$date_from)
//     // {
//     //     $equips_filtred = [];

//     //     if(empty($date_from))
//     //     {
//     //         $this->date_from = date('Y-m-d H:i:s', strtotime('-24 hours', time()));

//     //     } else {
//     //         $this->date_from = date('Y-m-d H:i:s',strtotime($this->date_from.'-24 hours'));
//     //     }

//     //     foreach ($equips_histories as $equip) {

//     //         if($equip->start_time >= $this->date_from)
//     //         {
//     //             array_push($equips_filtred,$equip);
//     //         }

//     //     }

//     //     return $equips_filtred;
//     // }

//     // public function filterByDateTo($equips_histories,$date_to)
//     // {
//     //     $equips_filtred = [];

//     //     if(empty($date_to))
//     //     {
//     //         $this->date_to = date('Y-m-d H:i:s');
//     //     }

//     //     foreach ($equips_histories as $equip) {

//     //         if($equip->end_time <= $this->date_to)
//     //         {
//     //             array_push($equips_filtred,$equip);
//     //         }

//     //     }

//     //     return $equips_filtred;
//     // }

//     // public function getEquipsGroups($equips_names)
//     // {
//     //     $groups = [];
//     //     $boxes = $this->getBoxes();
//     //     $all_groups = [];

//     //     foreach ($boxes as $box) {

//     //         $group = [];

//     //         foreach ($equips_names as $equip) {

//     //             if($equip->host_object_id == $box->host_object_id)
//     //             {
//     //                 array_push($group,$equip);
//     //             }
//     //         }

//     //         array_push($groups,$group);
//     //     }

//     //     $equips = [];

//     //     for ($i=0; $i < sizeof($groups); $i++) {

//     //         foreach ($groups[$i] as $gp) {

//     //             array_push($equips,$gp->equip_name);

//     //         }

//     //         array_push($all_groups,(object)['box_name' => $groups[$i][0]->box_name, 'equips' => $equips]);

//     //         $equips = [];
//     //     }

//     //     return $all_groups;
//     // }

//     public function getBoxes()
//     {
//         

//         if ($this->site_name == 'All') {

//             return DB::table('nagios_hosts')
//                 ->where('alias','box')
//                 ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
//                 ->orderBy('display_name')
//                 ->get();
//         }
//         else
//         {
//             return DB::table('nagios_hosts')
//                 ->where('alias','box')
//                 ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
//                 ->where('nagios_customvariables.varvalue',$this->site_name)
//                 ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
//                 ->orderBy('display_name')
//                 ->get();
//         }

//     }
//}
