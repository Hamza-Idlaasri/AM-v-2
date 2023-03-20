<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use App\Models\EquipsNames;
use App\Models\EquipsDetail;

class Equips extends Component
{
    // Site Name
    public $site_name;

    // Filter
    public $equip_name;
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
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $this->getHistory();
        
        $this->equips_status = [$this->equips_ok, $this->equips_warning, $this->equips_critical, $this->equips_unknown];

        return view('livewire.statistic.equips')
            ->with(['equips_status' => $this->equips_status, 'equips_names' => $this->getEquipsGroups()])
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
        
    //     $this->OrganizeStates($equips_ranges);
    // }

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
                for ($i=0; $i < sizeof($checks_of_equip); $i++) {
                    
                    if ($i < (sizeof($checks_of_equip)-1)) {

                        if ($checks_of_equip[$i]->state == $checks_of_equip[$i+1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set end_time of equip check to the last end_time of state
                            // $checks_of_equip[$start_index]->end_time = $checks_of_equip[$end_index]->end_time;

                            // push the state in table
                            array_push($equips_range_of_states, $checks_of_equip[$start_index]->state);

                            // reset the start_index var
                            $start_index = $i+1;
                        }

                    } else {
                        if ($checks_of_equip[$i]->state == $checks_of_equip[$i-1]->state) {

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

    public function SortStatus($ranges)
    {  
        $this->equips_ok = 0;
        $this->equips_warning = 0;
        $this->equips_critical = 0;
        $this->equips_unknown = 0;

        foreach ($ranges as $state) {
            
            switch ($state) {
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
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as pin_name','ed.equip_name','nagios_services.service_object_id','nagios_statehistory.statehistory_id','nagios_statehistory.last_state','nagios_statehistory.state','nagios_statehistory.state_time','nagios_statehistory.state_time_usec','nagios_statehistory.output')
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
    
        return $this->SortStatus($collection);

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





































    // public function render()
    // {
    //     $equips_name = $this->getEquipsName()->get();
        
    //     $equips_status = $this->getEquipsStatus($equips_name);

    //     $datasets = $this->getChartRange();
    //     // dd($equips_status,$datasets);
    //     return view('livewire.statistic.equips')
    //         ->with(['equips_status' => $equips_status, 'datasets' => $datasets])
    //         ->extends('layouts.app')
    //         ->section('content');
    // }

    // public function getEquipsChecks()
    // {
    //     $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

    //     // $date = date('Y-m-d H:i:s', strtotime('-24 hours', time()));

    //     if ($site_name == 'All') {
            
    //         return DB::table('nagios_servicechecks')
    //             ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //             ->select('nagios_hosts.alias','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.state','nagios_servicechecks.servicecheck_id','nagios_servicechecks.start_time','nagios_servicechecks.end_time')
    //             ->where('alias','box')
    //             ->orderByDesc('nagios_services.display_name')
    //             ->orderBy('start_time');
    //             // ->where('nagios_servicechecks.end_time','>=',$date);
    //     }
    //     else
    //     {
    //         return DB::table('nagios_servicechecks')
    //             ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
    //             ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->select('nagios_hosts.alias','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.state','nagios_servicechecks.servicecheck_id','nagios_servicechecks.start_time','nagios_servicechecks.end_time')
    //             ->where('alias','box')
    //             ->where('nagios_customvariables.varvalue',$site_name)
    //             ->orderByDesc('nagios_services.display_name')
    //             ->orderBy('start_time');
    //             // ->where('nagios_servicechecks.end_time','>=',$date);
    //     }
        

    // }

    // public function getEquipsName()
    // {
    //     $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

    //     if ($site_name == 'All') {
            
    //         return DB::table('nagios_hosts')
    //             ->where('alias','box')
    //             ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //             ->select('nagios_services.display_name as box_name','nagios_services.service_object_id','nagios_services.display_name as equip_name');
    //     }
    //     else
    //     {
    //         return DB::table('nagios_hosts')
    //             ->where('alias','box')
    //             ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
    //             ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
    //             ->where('nagios_customvariables.varvalue',$site_name)
    //             ->select('nagios_services.display_name as box_name','nagios_services.service_object_id','nagios_services.display_name as equip_name');
    //     }
        
    // }

    // public function getEquipsStatus($equips_name)
    // {
    //     $equips_ok = 0;
    //     $equips_warning = 0;
    //     $equips_critical = 0;
    //     $equips_unknown = 0;

    //     $equips_checks = [];
        
    //     foreach ($equips_name as $equip) {

    //         $all_equips_checks = $this->getEquipsChecks()
    //             ->where('nagios_servicechecks.service_object_id','=',$equip->service_object_id)
    //             ->take(2)
    //             ->get();

    //         if(!empty($all_equips_checks))
    //         {
    //             $status = $this->getInterval($all_equips_checks);

    //             for ($i=0; $i < sizeof($status); $i++) {

    //                 $equip = $this->getEquipsChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][0])->first();

    //                 if(!empty($equip)) {
    //                     array_push($equips_checks,$equip);
    //                 }
                
    //             }

    //         } else {
    //             continue;
    //         }

    //     }

    //     foreach ($equips_checks as $equip) {
            
    //         switch ($equip->state) {
                
    //             case 0:
    //                 $equips_ok++;
    //                 break;
                
    //             case 1:
    //                 $equips_warning++;
    //                 break;
                
    //             case 2:
    //                 $equips_critical++;
    //                 break;
                
    //             case 3:
    //                 $equips_unknown++;
    //                 break;
    //         }
    //     }

    //     return (object)['equips_ok' => $equips_ok,'equips_warning' => $equips_warning,'equips_critical' => $equips_critical,'equips_unknown' => $equips_unknown];
    // }

    // public function getInterval($equip)
    // {
    //     $status = [];

    //     $interval = [];

    //     for ($i=0; $i < sizeof($equip); $i++) { 
                
    //         if($i == 0)
    //         {
    //             array_push($interval,$equip[0]->servicecheck_id);
    //         }

    //         if ($i > 0 && $i < sizeof($equip)-1) {
                
    //             if($equip[$i]->state == $equip[$i-1]->state)
    //             {
    //                 continue;

    //             } else {

    //                 array_push($interval,$equip[$i-1]->servicecheck_id);

    //                 array_push($status,$interval);

    //                 $interval = [];

    //                 array_push($interval,$equip[$i]->servicecheck_id);

    //             }

    //         }

    //         if($i == sizeof($equip)-1)
    //         {
    //             if($equip[$i]->state == $equip[$i-1]->state)
    //             {
    //                 array_push($interval,$equip[$i]->servicecheck_id);
    //                 array_push($status,$interval);

    //             } else {

    //                 array_push($interval,$equip[$i-1]->servicecheck_id);
    //                 array_push($status,$interval);

    //                 $interval = [];

    //                 array_push($interval,$equip[$i]->servicecheck_id);
    //                 array_push($interval,$equip[$i]->servicecheck_id);
    //                 array_push($status,$interval);
    //             }
    //         }

    //     }

    //     return $status;
    // }

    // public function getChartRange()
    // {
    //     $datasets = [];

    //     $equips = $this->getEquipsName()->get();

    //     $data = [
    //         'equip_name' => '',
    //         'Ok' => '',
    //         'Warning' => '',
    //         'Critical' => '',
    //         'Unknown' => '',
    //     ];

    //     foreach ($equips as $equip) {

    //         // Get All host checks
    //         $equip_checks = $this->getEquipsChecks()->where('nagios_services.display_name', $equip->equip_name)->get();

    //         // Get Ranges
    //         $range = [];
    //         $equip_ranges = [];

    //         if (sizeof($equip_checks)) {
            
    //             for ($i=0; $i < sizeof($equip_checks); $i++) {
                    
    //                 if ($i == 0) {
    //                     array_push($range, $equip_checks[0]);
    //                 }

    //                 if ($i > 0 && $i < sizeof($equip_checks)-1) {

    //                     if ($equip_checks[$i]->state == $equip_checks[$i-1]->state) {
    //                         continue;
    //                     } 
    //                     else
    //                     {
    //                         array_push($range,$equip_checks[$i-1]);
    //                         array_push($equip_ranges,$range);
    //                         $range = [];
    //                         array_push($range,$equip_checks[$i]);
    //                     }

    //                 }

    //                 if ($i == sizeof($equip_checks)-1) {
                        
    //                     if ($equip_checks[$i]->state == $equip_checks[$i-1]->state) {
    //                         array_push($range,$equip_checks[$i]);
    //                         array_push($equip_ranges,$range);
    //                         $range = [];
    //                     }
    //                     else
    //                     {
    //                         array_push($range,$equip_checks[$i-1]);
    //                         array_push($equip_ranges,$range);
    //                         $range = [];
    //                         array_push($range,$equip_checks[$i]);
    //                         array_push($range,$equip_checks[$i]);
    //                         array_push($equip_ranges,$range);
    //                         $range = [];
    //                     }
    //                 }
    //             }

    //             // Make datasets        
    //             $ok = [];
    //             $warning = [];
    //             $critical = [];
    //             $unknown = [];

    //             for ($i=0; $i < sizeof($equip_ranges); $i++) { 
                    
    //                 if ($i == 0) {
    //                     $equip_name = $equip_ranges[0][0]->service_name.' ('.$equip_ranges[0][0]->host_name.')';
    //                 }

    //                 switch ($equip_ranges[$i][0]->state) {
                        
    //                     case 0:
    //                         array_push($ok, [$equip_ranges[$i][0]->start_time,$equip_ranges[$i][1]->end_time]);
    //                         break;

    //                     case 1:
    //                         array_push($warning, [$equip_ranges[$i][0]->start_time,$equip_ranges[$i][1]->end_time]);
    //                         break;

    //                     case 2:
    //                         array_push($critical, [$equip_ranges[$i][0]->start_time,$equip_ranges[$i][1]->end_time]);
    //                         break;

    //                     case 3:
    //                         array_push($unknown, [$equip_ranges[$i][0]->start_time,$equip_ranges[$i][1]->end_time]);
    //                         break;
    //                 }
    //             }

    //             $data = [
    //                 'equip_name' => $equip_name,
    //                 'Ok' => $ok,
    //                 'Warning' => $warning,
    //                 'Critical' => $critical,
    //                 'Unknown' => $unknown,
    //             ];
    //         }

    //         array_push($datasets,$data);
    //     }

    //     return $datasets;

    // }
}
