<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Hosts extends Component
{
    public $status = 'all';
    public $host_name;
    public $date_from;
    public $date_to;

    // protected $queryString = ['status','host_name','date_from','date_to'];

    public function render()
    {
        $hosts_histories = [];

        $hosts_names = $this->getHostsName()->get();
        
        foreach ($hosts_names as $host) {

            $all_hosts_checks = $this->getHostsChecks()
            ->where('nagios_hostchecks.host_object_id','=',$host->host_object_id)
            ->get();

            if(sizeof($all_hosts_checks))
            {
                $status = $this->getStatus($all_hosts_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $host_checks = $this->getHostsChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][0])->get();
                    
                    $end_host_checks = $this->getHostsChecks()->where('nagios_hostchecks.hostcheck_id','=',$status[$i][1])->get();

                    $host_checks[0]->end_time = $end_host_checks[0]->end_time;

                    array_push($hosts_histories,$host_checks[0]);
                }

            } else {

                continue;
            }

        }

        if($this->status != 'all')
        {
            $hosts_histories = $this->filterByStatus($hosts_histories,$this->status);
        }
            
        if ($this->host_name) 
        {
            $hosts_histories = $this->filterByName($hosts_histories,$this->host_name);
        }

        if($this->date_from)
        {
            $hosts_histories = $this->filterByDateFrom($hosts_histories,$this->date_from);
        }
        
        if($this->date_to)
        {
            $hosts_histories = $this->filterByDateTo($hosts_histories,$this->date_to);
        }

        return view('livewire.historic.hosts')
            ->with(['hosts_histories' => $hosts_histories,'hosts_names' => $hosts_names])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getHostsChecks()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hostchecks')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_hostchecks.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('alias','host')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.*','nagios_hosts.host_object_id','nagios_hostchecks.*')
            ->where('is_raw_check','=', 0);
    }

    public function getHostsName()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
            ->orderBy('display_name');
    }

    public function getStatus($host)
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

    public function filterByStatus($hosts_histories,$status)
    {
        $hosts_filtred = [];

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

        foreach ($hosts_histories as $host) {
            
            if($host->state == $status)
            {
                array_push($hosts_filtred,$host);
            }

        }

        return $hosts_filtred;
    }

    public function filterByName($hosts_histories,$name)
    {
        $hosts_filtred = [];

        foreach ($hosts_histories as $host) {
            
            if($host->display_name == $name)
            {
                array_push($hosts_filtred,$host);
            }

        }

        return $hosts_filtred;
    }

    public function filterByDateFrom($hosts_histories,$date_from)
    {
        $hosts_filtred = [];

        foreach ($hosts_histories as $host) {
            
            if($host->start_time >= $this->date_from)
            {
                array_push($hosts_filtred,$host);
            }

        }
    
        return $hosts_filtred;
    }

    public function filterByDateTo($hosts_histories,$date_to)
    {
        $hosts_filtred = [];

        foreach ($hosts_histories as $host) {
            
            if($host->end_time <= $this->date_to)
            {
                array_push($hosts_filtred,$host);
            }

        }
    
        return $hosts_filtred;
    }
}
