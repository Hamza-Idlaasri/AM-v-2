<?php

namespace App\Http\Livewire\Statistic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Equips extends Component
{
    public function render()
    {
        $equips_name = $this->getEquipsName()->get();
        
        $equips_status = $this->getEquipsStatus($equips_name);

        $datasets = $this->getChartRange();

        return view('livewire.statistic.equips')
            ->with(['equips_status' => $equips_status, 'datasets' => $datasets])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getEquipsChecks()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_servicechecks')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->select('nagios_hosts.alias','nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.*')
            ->where('alias','box')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->orderByDesc('nagios_services.display_name')
            ->orderBy('start_time');

    }

    public function getEquipsName()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
            ->where('alias','box')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_services.display_name as box_name','nagios_services.service_object_id','nagios_services.display_name as equip_name');
    }

    public function getEquipsStatus($equips_name)
    {
        $equips_ok = 0;
        $equips_warning = 0;
        $equips_critical = 0;
        $equips_unknown = 0;

        $equips_checks = [];
        
        foreach ($equips_name as $equip) {

            $all_equips_checks = $this->getEquipsChecks()
                ->where('nagios_servicechecks.service_object_id','=',$equip->service_object_id)
                ->get();

            if(sizeof($all_equips_checks))
            {
                $status = $this->getInterval($all_equips_checks);  

                for ($i=0; $i < sizeof($status); $i++) {
                    
                    $equip = $this->getEquipsChecks()->where('nagios_servicechecks.servicecheck_id','=',$status[$i][0])->get();
                    array_push($equips_checks,$equip[0]);
                
                }

            } else {
                continue;
            }

        }

        foreach ($equips_checks as $equip) {
            

            switch ($equip->state) {
                
                case 0:
                    $equips_ok++;
                    break;
                
                case 1:
                    $equips_warning++;
                    break;
                
                case 2:
                    $equips_critical++;
                    break;
                
                case 3:
                    $equips_unknown++;
                    break;
            }
        }

        return (object)['equips_ok' => $equips_ok,'equips_warning' => $equips_warning,'equips_critical' => $equips_critical,'equips_unknown' => $equips_unknown];
    }

    public function getInterval($equip)
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

    public function getChartRange()
    {
        $datasets = [];

        $equips = $this->getEquipsName()->get();

        foreach ($equips as $equip) {

            // Get All host checks
            $equip_checks = $this->getEquipsChecks()->where('nagios_services.display_name', $equip->equip_name)->get();

            // Get Ranges
            $range = [];
            $equip_ranges = [];

            if (sizeof($equip_checks)) {
            
                for ($i=0; $i < sizeof($equip_checks); $i++) {
                    
                    if ($i == 0) {
                        array_push($range, $equip_checks[0]);
                    }

                    if ($i > 0 && $i < sizeof($equip_checks)-1) {

                        if ($equip_checks[$i]->state == $equip_checks[$i-1]->state) {
                            continue;
                        } 
                        else
                        {
                            array_push($range,$equip_checks[$i-1]);
                            array_push($equip_ranges,$range);
                            $range = [];
                            array_push($range,$equip_checks[$i]);
                        }

                    }

                    if ($i == sizeof($equip_checks)-1) {
                        
                        if ($equip_checks[$i]->state == $equip_checks[$i-1]->state) {
                            array_push($range,$equip_checks[$i]);
                            array_push($equip_ranges,$range);
                            $range = [];
                        }
                        else
                        {
                            array_push($range,$equip_checks[$i-1]);
                            array_push($equip_ranges,$range);
                            $range = [];
                            array_push($range,$equip_checks[$i]);
                            array_push($range,$equip_checks[$i]);
                            array_push($equip_ranges,$range);
                            $range = [];
                        }
                    }
                }

                // Make datasets        
                $ok = [];
                $warning = [];
                $critical = [];
                $unknown = [];

                for ($i=0; $i < sizeof($equip_ranges); $i++) { 
                    
                    if ($i == 0) {
                        $equip_name = $equip_ranges[0][0]->service_name.' ('.$equip_ranges[0][0]->host_name.')';
                    }

                    switch ($equip_ranges[$i][0]->state) {
                        
                        case 0:
                            array_push($ok, [$equip_ranges[$i][0]->start_time,$equip_ranges[$i][1]->end_time]);
                            break;

                        case 1:
                            array_push($warning, [$equip_ranges[$i][0]->start_time,$equip_ranges[$i][1]->end_time]);
                            break;

                        case 2:
                            array_push($critical, [$equip_ranges[$i][0]->start_time,$equip_ranges[$i][1]->end_time]);
                            break;

                        case 3:
                            array_push($unknown, [$equip_ranges[$i][0]->start_time,$equip_ranges[$i][1]->end_time]);
                            break;
                    }
                }

                $data = [
                    'equip_name' => $equip_name,
                    'Ok' => $ok,
                    'Warning' => $warning,
                    'Critical' => $critical,
                    'Unknown' => $unknown,
                ];
            }

            array_push($datasets,$data);
        }

        return $datasets;

    }
}
