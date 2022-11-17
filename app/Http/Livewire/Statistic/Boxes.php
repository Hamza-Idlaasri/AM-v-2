<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Boxes extends Component
{
    public function render()
    {
        $boxes_name = $this->getBoxesName()->get();
        
        $boxes_status = $this->getBoxesStatus($boxes_name);

        $datasets = $this->getChartRange();

        return view('livewire.statistic.boxes')
            ->with(['boxes_status' => $boxes_status,'datasets' => $datasets])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getBoxesChecks()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $date = date('Y-m-d H:i:s', strtotime('-24 hours', time()));

        if ($site_name == 'All') {
            
            return DB::table('nagios_hostchecks')
                ->select('nagios_hosts.*','nagios_hosts.host_object_id','nagios_hostchecks.*')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
                ->where('alias','box')
                ->where('is_raw_check','=', 0)
                ->orderBy('start_time')
                ->where('nagios_hostchecks.end_time','>=',$date);
        }
        else
        {
            return DB::table('nagios_hostchecks')
                ->select('nagios_hosts.*','nagios_hosts.host_object_id','nagios_hostchecks.*')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('alias','box')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->where('is_raw_check','=', 0)
                ->orderBy('start_time')
                ->where('nagios_hostchecks.end_time','>=',$date);
        }
        
    }

    public function getBoxesName()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == 'All') {
            
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
                ->orderBy('display_name');
        }
        else
        {
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
                ->orderBy('display_name');
        }
        
    }

    public function getBoxesStatus($boxes_name)
    {
        $boxes_up = 0;
        $boxes_down = 0;
        $boxes_unreachable = 0;

        $boxes_checks = [];
        
        foreach ($boxes_name as $box) {

            $all_boxes_checks = $this->getBoxesChecks()
                ->where('nagios_hostchecks.host_object_id','=',$box->host_object_id)
                ->get();

            if(sizeof($all_boxes_checks))
            {
                $status = $this->getInterval($all_boxes_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $box = $this->getBoxesChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][0])->get();
                    array_push($boxes_checks,$box[0]);
                
                }

            } else {
                continue;
            }

        }

        foreach ($boxes_checks as $box) {
            

            switch ($box->state) {
                
                case 0:
                    $boxes_up++;
                    break;
                
                case 1:
                    $boxes_down++;
                    break;
                
                case 2:
                    $boxes_unreachable++;
                    break;
            }
        }

        return (object)['boxes_up' => $boxes_up,'boxes_down' => $boxes_down,'boxes_unreachable' => $boxes_unreachable];
    }

    public function getInterval($box)
    {
        $status = [];

        $interval = [];

        for ($i=0; $i < sizeof($box); $i++) { 
                
            if($i == 0)
            {
                array_push($interval,$box[0]->hostcheck_id);
            }

            if ($i > 0 && $i < sizeof($box)-1) {
                
                if($box[$i]->state == $box[$i-1]->state)
                {
                    continue;

                } else {

                    array_push($interval,$box[$i-1]->hostcheck_id);

                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$box[$i]->hostcheck_id);

                }

            }

            if($i == sizeof($box)-1)
            {
                if($box[$i]->state == $box[$i-1]->state)
                {
                    array_push($interval,$box[$i]->hostcheck_id);
                    array_push($status,$interval);

                } else {

                    array_push($interval,$box[$i-1]->hostcheck_id);
                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$box[$i]->hostcheck_id);
                    array_push($interval,$box[$i]->hostcheck_id);
                    array_push($status,$interval);
                }
            }

        }

        return $status;
    }

    public function getChartRange()
    {
        $datasets = [];

        $boxes = $this->getBoxesName()->get();

        $data = [
            'host_name' => '',
            'Up' => '',
            'Down' => '',
            'Unreachable' => '',
        ];

        foreach ($boxes as $box) {

            // Get All box checks
            $box_checks = $this->getBoxesChecks()->where('nagios_hosts.display_name', $box->host_name)->get();

            // Get Ranges
            $range = [];
            $box_ranges = [];

            if (sizeof($box_checks)) {
            
                for ($i=0; $i < sizeof($box_checks); $i++) {
                    
                    if ($i == 0) {
                        array_push($range, $box_checks[0]);
                    }

                    if ($i > 0 && $i < sizeof($box_checks)-1) {

                        if ($box_checks[$i]->state == $box_checks[$i-1]->state) {
                            continue;
                        } 
                        else
                        {
                            array_push($range,$box_checks[$i-1]);
                            array_push($box_ranges,$range);
                            $range = [];
                            array_push($range,$box_checks[$i]);
                        }

                    }

                    if ($i == sizeof($box_checks)-1) {
                        
                        if ($box_checks[$i]->state == $box_checks[$i-1]->state) {
                            array_push($range,$box_checks[$i]);
                            array_push($box_ranges,$range);
                            $range = [];
                        }
                        else
                        {
                            array_push($range,$box_checks[$i-1]);
                            array_push($box_ranges,$range);
                            $range = [];
                            array_push($range,$box_checks[$i]);
                            array_push($range,$box_checks[$i]);
                            array_push($box_ranges,$range);
                            $range = [];
                        }
                    }
                }

                // Make datasets        
                $up = [];
                $down = [];
                $unreach = [];

                for ($i=0; $i < sizeof($box_ranges); $i++) { 
                    
                    if ($i == 0) {
                        $box_name = $box_ranges[0][0]->display_name;
                    }

                    switch ($box_ranges[$i][0]->state) {
                        
                        case 0:
                            array_push($up, [$box_ranges[$i][0]->start_time,$box_ranges[$i][1]->end_time]);
                            // array_push($up, $box_ranges[$i][0]->start_time);
                            // array_push($up, $box_ranges[$i][1]->end_time);
                            break;

                        case 1:
                            array_push($down, [$box_ranges[$i][0]->start_time,$box_ranges[$i][1]->end_time]);
                            // array_push($down, $box_ranges[$i][0]->start_time);
                            // array_push($down, $box_ranges[$i][1]->end_time);
                            break;

                        case 2:
                            array_push($unreach, [$box_ranges[$i][0]->start_time,$box_ranges[$i][1]->end_time]);
                            // array_push($unreach, $box_ranges[$i][0]->start_time);
                            // array_push($unreach, $box_ranges[$i][1]->end_time);
                            break;
                    }
                }

                $data = [
                    'box_name' => $box_name,
                    'Up' => $up,
                    'Down' => $down,
                    'Unreachable' => $unreach,
                ];
            }

            array_push($datasets,$data);
        }

        return $datasets;

    }
}

