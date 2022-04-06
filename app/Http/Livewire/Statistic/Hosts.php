<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Hosts extends Component
{
    public function render()
    {
        $hosts_names = $this->getHostsName()->get();

        $hosts_status = $this->getHostsStatus($hosts_names);

        $datasets = $this->getChartRange();

        $min = date("Y-m-d", strtotime($this->getHostsChecks()->first()->start_time));
        $max = date("Y-m-d", strtotime($this->getHostsChecks()->orderByDesc('start_time')->first()->end_time.'+ 1 days'));

        return view('livewire.statistic.hosts')
            ->with(['hosts_status' => $hosts_status, 'datasets' => $datasets,'min' => $min, 'max' => $max])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getHostsChecks()
    {
        return DB::table('nagios_hostchecks')
            ->select('nagios_hosts.*','nagios_hosts.host_object_id','nagios_hostchecks.*')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
            ->where('alias','host')
            ->where('is_raw_check','=', 0);
    }

    public function getHostsName()
    {
        return DB::table('nagios_hosts')
            ->where('alias','host')
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
            ->orderBy('display_name');
    }

    public function getHostsStatus($hosts_names)
    {
        $hosts_up = 0;
        $hosts_down = 0;
        $hosts_unreachable = 0;

        $hosts_checks = [];

        foreach ($hosts_names as $host) {

            $host_check = $this->getHostsChecks()
                ->where('nagios_hostchecks.host_object_id','=',$host->host_object_id)
                ->get();

            if(sizeof($host_check))
            {
                $status = $this->getInterval($host_check);

                for ($i=0; $i < sizeof($status); $i++) {

                    $host = $this->getHostsChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][0])->get();
                    array_push($hosts_checks,$host[0]);

                }

            } else {
                continue;
            }

        }

        foreach ($hosts_checks as $host) {

            switch ($host->state) {

                case 0:
                    $hosts_up++;
                    break;

                case 1:
                    $hosts_down++;
                    break;

                case 2:
                    $hosts_unreachable++;
                    break;
            }
        }

        return (object)['hosts_up' => $hosts_up,'hosts_down' => $hosts_down,'hosts_unreachable' => $hosts_unreachable];
    }

    public function getInterval($host)
    {
        $status = [];

        $interval = [];

        for ($i=0; $i < sizeof($host); $i++) {

            if($i == 0)
            {
                array_push($interval,$host[0]->hostcheck_id);
            }

            if ($i > 0 && $i < sizeof($host)-1) {

                if($host[$i]->state == $host[$i-1]->state)
                {
                    continue;

                } else {

                    array_push($interval,$host[$i-1]->hostcheck_id);

                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$host[$i]->hostcheck_id);

                }

            }

            if($i == sizeof($host)-1)
            {
                if($host[$i]->state == $host[$i-1]->state)
                {
                    array_push($interval,$host[$i]->hostcheck_id);
                    array_push($status,$interval);

                } else {

                    array_push($interval,$host[$i-1]->hostcheck_id);
                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$host[$i]->hostcheck_id);
                    array_push($interval,$host[$i]->hostcheck_id);
                    array_push($status,$interval);
                }
            }

        }

        return $status;
    }

    public function getChartRange()
    {
        $datasets = [];

        $hosts = $this->getHostsName()->get();
            
        foreach ($hosts as $host) {

            // Get All host checks
            $host_checks = $this->getHostsChecks()->where('nagios_hosts.display_name', $host->host_name)->get();

            // Get Ranges
            $range = [];
            $host_ranges = [];

            if (sizeof($host_checks)) {
            
                for ($i=0; $i < sizeof($host_checks); $i++) {
                    
                    if ($i == 0) {
                        array_push($range, $host_checks[0]);
                    }

                    if ($i > 0 && $i < sizeof($host_checks)-1) {

                        if ($host_checks[$i]->state == $host_checks[$i-1]->state) {
                            continue;
                        } 
                        else
                        {
                            array_push($range,$host_checks[$i-1]);
                            array_push($host_ranges,$range);
                            $range = [];
                            array_push($range,$host_checks[$i]);
                        }

                    }

                    if ($i == sizeof($host_checks)-1) {
                        
                        if ($host_checks[$i]->state == $host_checks[$i-1]->state) {
                            array_push($range,$host_checks[$i]);
                            array_push($host_ranges,$range);
                            $range = [];
                        }
                        else
                        {
                            array_push($range,$host_checks[$i-1]);
                            array_push($host_checks,$range);
                            $range = [];
                            array_push($range,$host_checks[$i]);
                            array_push($range,$host_checks[$i]);
                            array_push($host_ranges,$range);
                            $range = [];
                        }
                    }
                }

                // Make datasets        
                $up = [];
                $down = [];
                $unreach = [];

                for ($i=0; $i < sizeof($host_ranges); $i++) { 
                    
                    if ($i == 0) {
                        $host_name = $host_ranges[0][0]->display_name;
                    }

                    switch ($host_ranges[$i][0]->state) {
                        
                        case 0:
                            array_push($up, [$host_ranges[$i][0]->start_time,$host_ranges[$i][1]->end_time]);
                            // array_push($up, $host_ranges[$i][0]->start_time);
                            // array_push($up, $host_ranges[$i][1]->end_time);
                            break;

                        case 1:
                            array_push($down, [$host_ranges[$i][0]->start_time,$host_ranges[$i][1]->end_time]);
                            // array_push($down, $host_ranges[$i][0]->start_time);
                            // array_push($down, $host_ranges[$i][1]->end_time);
                            break;

                        case 2:
                            array_push($unreach, [$host_ranges[$i][0]->start_time,$host_ranges[$i][1]->end_time]);
                            // array_push($unreach, $host_ranges[$i][0]->start_time);
                            // array_push($unreach, $host_ranges[$i][1]->end_time);
                            break;
                    }
                }

               
                $data = [
                    'host_name' => $host_name,
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
