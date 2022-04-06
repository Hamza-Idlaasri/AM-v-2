<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Equips extends Component
{
    public $status = 'all';
    public $equip_name;
    public $date_from;
    public $date_to;

    public function render()
    {
        $equips_names = $this->getEquipsName()->get();
        
        $equips_histories = [];

        foreach ($equips_names as $equip) {

            $all_equips_checks = $this->getEquipsChecks()
                ->where('nagios_servicechecks.service_object_id','=',$equip->service_object_id)
                ->get();

            if(sizeof($all_equips_checks))
            {
                $status = $this->getStatus($all_equips_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $equip_checks = $this->getEquipsChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][0])->get();
                    
                    $end_host_checks = $this->getEquipsChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][1])->get();

                    $equip_checks[0]->end_time = $end_host_checks[0]->end_time;

                    array_push($equips_histories,$equip_checks[0]);
                }

            } else {
                            
                continue;
            }

        }

        if($this->status != 'all')
        {
            $equips_histories = $this->filterByStatus($equips_histories,$this->status);
        }
            
        if ($this->equip_name) 
        {
            $equips_histories = $this->filterByName($equips_histories,$this->equip_name);
        }

        if($this->date_from)
        {
            $equips_histories = $this->filterByDateFrom($equips_histories,$this->date_from);
        }
        
        if($this->date_to)
        {
            $equips_histories = $this->filterByDateTo($equips_histories,$this->date_to);
        }

        return view('livewire.historic.equips')
            ->with(['equips_histories' => $equips_histories, 'equips_names' => $this->getEquipsGroups($equips_names)])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getEquipsChecks()
    {
        return DB::table('nagios_servicechecks')
        ->select('nagios_hosts.alias','nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicechecks.*')
        ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
        ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
        ->where('alias','box');
    }

    public function getEquipsName()
    {
        return DB::table('nagios_hosts')
            ->where('alias','box')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->select('nagios_hosts.host_object_id','nagios_hosts.display_name as box_name','nagios_services.service_object_id','nagios_services.display_name as equip_name');
    }

    public function getStatus($equip)
    {
        $status = [];

        $interval = [];

        for ($i=0; $i < sizeof($equip); $i++) { 
                
            if($i == 0)
            {
                array_push($interval,$equip[0]->servicecheck_id);
            }

            if ($i > 0 && $i < sizeof($equip)-1) {
                
                if($equip[$i]->state == $equip[$i-1]->state)
                {
                    continue;

                } else {

                    array_push($interval,$equip[$i-1]->servicecheck_id);

                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$equip[$i]->servicecheck_id);

                }

            }

            if($i == sizeof($equip)-1)
            {
                if($equip[$i]->state == $equip[$i-1]->state)
                {
                    array_push($interval,$equip[$i]->servicecheck_id);
                    array_push($status,$interval);

                } else {

                    array_push($interval,$equip[$i-1]->servicecheck_id);
                    array_push($status,$interval);

                    $interval = [];

                    array_push($interval,$equip[$i]->servicecheck_id);
                    array_push($interval,$equip[$i]->servicecheck_id);
                    array_push($status,$interval);
                }
            }

        }

        return $status;
    }

    public function filterByStatus($equips_histories,$status)
    {
        $equips_filtred = [];

        switch ($status) {
            case 'ok':
                $status = 0;
                break;
            case 'warning':
                $status = 1;
                break;
            case 'critical':
                $status = 2;
                break;
            case 'unknown':
                $status = 3;
                break;
        }

        foreach ($equips_histories as $equip) {
            
            if($equip->state == $status)
            {
                array_push($equips_filtred,$equip);
            }

        }

        return $equips_filtred;
    }

    public function filterByName($equips_histories,$name)
    {
        $equips_filtred = [];

        foreach ($equips_histories as $equip) {
            
            if($equip->equip_name == $name)
            {
                array_push($equips_filtred,$equip);
            }

        }

        return $equips_filtred;
    }

    public function filterByDateFrom($equips_histories,$date_from)
    {
        $equips_filtred = [];

        foreach ($equips_histories as $equip) {
            
            if($equip->start_time >= $this->date_from)
            {
                array_push($equips_filtred,$equip);
            }

        }
    
        return $equips_filtred;
    }

    public function filterByDateTo($equips_histories,$date_to)
    {
        $equips_filtred = [];

        foreach ($equips_histories as $equip) {
            
            if($equip->end_time <= $this->date_to)
            {
                array_push($equips_filtred,$equip);
            }

        }
    
        return $equips_filtred;
    }

    public function getEquipsGroups($equips_names)
    {
        $groups = [];
        $boxes = $this->getBoxes();
        $all_groups = [];

        foreach ($boxes as $box) {

            $group = [];

            foreach ($equips_names as $equip) {
                
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
        return DB::table('nagios_hosts')
            ->where('alias','box')
            ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
            ->orderBy('display_name')
            ->get();
    }
}
