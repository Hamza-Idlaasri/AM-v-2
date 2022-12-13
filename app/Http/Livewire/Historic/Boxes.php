<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Boxes extends Component
{
    public $status = 'all';
    public $box_name;
    public $date_from;
    public $date_to;

    // protected $queryString = ['status','box_name','date_from','date_to'];

    public function render()
    {
        $boxes_names = $this->getBoxesName()->get();
        
        $boxes_histories = [];

        foreach ($boxes_names as $box) {

            $all_boxes_checks = $this->getBoxesChecks()->where('nagios_hostchecks.host_object_id','=',$box->host_object_id)->get();

            if(sizeof($all_boxes_checks))
            {
                $status = $this->getStatus($all_boxes_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $box_checks = $this->getBoxesChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][0])->get();
                    
                    $end_host_checks = $this->getBoxesChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][1])->get();

                    $box_checks[0]->end_time = $end_host_checks[0]->end_time;

                    array_push($boxes_histories,$box_checks[0]);
                }

            } else {
                            
                continue;
            }

        }

        if($this->status != 'all')
        {
            $boxes_histories = $this->filterByStatus($boxes_histories,$this->status);
        }
            
        if ($this->box_name) 
        {
            $boxes_histories = $this->filterByName($boxes_histories,$this->box_name);
        }

        foreach ($boxes_histories as $boxes) {

            unset($boxes->host_object_id);
            unset($boxes->hostcheck_id);

            switch ($boxes->state) {
                case 0:
                    $boxes->state = 'Up';
                    break;
                case 1:
                    $boxes->state = 'Down';
                    break;
                case 2:
                    $boxes->state = 'Unreachable';
                    break;
            }
        }
        
        return view('livewire.historic.boxes')
            ->with(['boxes_histories' => $boxes_histories,'boxes_names' => $boxes_names])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getBoxesChecks()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $from_date = $this->date_from;
        $to_date = $this->date_to;
        
        // Check from date
        if(empty($this->date_from))
        {
            $from_date = date('Y-m-d H:i:s', strtotime('-24 hours', time()));

        } else {
            $from_date = date('Y-m-d H:i:s', strtotime($from_date));
        }
        
        // Check to date
        if(empty($to_date))
        {
            if(empty($this->date_from))
            {
                $to_date = date('Y-m-d H:i:s');
                
            } else {
                $to_date = date('Y-m-d H:i:s', strtotime($from_date.'+24 hours'));
            }

        } else {
            if(empty($this->date_from))
            {
                $to_date = date('Y-m-d H:i:s', strtotime($to_date.'+22 hours'));
                $from_date = date('Y-m-d H:i:s', strtotime($to_date.'-24 hours'));
            } else {
                // TEMPRORY SOLUTION
                $to_date = date('Y-m-d H:i:s', strtotime($to_date.'+22 hours'));
                $from_date = date('Y-m-d H:i:s', strtotime($to_date.'-24 hours'));
            }
        }
        
        if ($site_name == 'All') {

            return DB::table('nagios_hostchecks')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
                ->where('alias','box')
                ->select('nagios_hosts.display_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                ->where('is_raw_check','=', 0)
                ->where('nagios_hostchecks.end_time','>=',$from_date)
                ->where('nagios_hostchecks.end_time','<=',$to_date);
        } else {

            return DB::table('nagios_hostchecks')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('alias','box')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.display_name','nagios_hosts.address','nagios_hosts.host_object_id','nagios_hostchecks.hostcheck_id','nagios_hostchecks.state','nagios_hostchecks.start_time','nagios_hostchecks.end_time','nagios_hostchecks.output')
                ->where('is_raw_check','=', 0)
                ->where('nagios_hostchecks.end_time','>=',$from_date)
                ->where('nagios_hostchecks.end_time','<=',$to_date);
        }
        
    }

    public function getBoxesName()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == 'All') {
            
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
                ->orderBy('display_name');
        }
        else
        {
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
                ->orderBy('display_name');
        }
    }

    public function getStatus($box)
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

    public function filterByStatus($boxes_histories,$status)
    {
        $boxes_filtred = [];

        switch ($status) {
            case 'up':
                $status = 0;
                break;
            case 'down':
                $status = 1;
                break;
            case 'unreachable':
                $status = 2;
                break;
        }

        foreach ($boxes_histories as $box) {
            
            if($box->state == $status)
            {
                array_push($boxes_filtred,$box);
            }

        }

        return $boxes_filtred;
    }

    public function filterByName($boxes_histories,$name)
    {
        $hosts_filtred = [];

        foreach ($boxes_histories as $host) {
            
            if($host->display_name == $name)
            {
                array_push($hosts_filtred,$host);
            }

        }

        return $hosts_filtred;
    }

    // public function filterByDateFrom($boxes_histories,$date_from)
    // {
    //     $hosts_filtred = [];

    //     foreach ($boxes_histories as $box) {
            
    //         if($box->start_time >= $this->date_from)
    //         {
    //             array_push($boxes_filtred,$box);
    //         }

    //     }
    
    //     return $boxes_filtred;
    // }

    // public function filterByDateTo($boxes_histories,$date_to)
    // {
    //     $boxes_filtred = [];

    //     foreach ($boxes_histories as $box) {
            
    //         if($box->end_time <= $this->date_to)
    //         {
    //             array_push($boxes_filtred,$box);
    //         }

    //     }
    
    //     return $boxes_filtred;
    // }
}
