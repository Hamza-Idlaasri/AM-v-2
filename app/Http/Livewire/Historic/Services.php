<?php

namespace App\Http\Livewire\Historic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Services extends Component
{
    use WithPagination;

    public $status = 'all';
    public $service_name;
    public $date_from;
    public $date_to;
    public $site_name;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $services_histories = $this->getStateRanges();

        // filter by state
        if($this->status != 'all')
        {
            foreach ($services_histories as $key => $service) {
                if ($service->state === $this->status) {
                    continue;
                } else {
                    unset($services_histories[$key]);
                }
            }

            $services_histories = array_values($services_histories);

        }    

        // filter by Name
        if ($this->service_name)
        {
            foreach ($services_histories as $key => $service) {
                if ($service->service_name == $this->service_name) {
                    continue;
                } else {
                    unset($services_histories[$key]);
                }
            }

            $services_histories = array_values($services_histories);
        }

        return view('livewire.historic.services')
            ->with(['services_histories' => $this->paginate($services_histories), 'services_names' => $this->getServicesGroups(),'download' => $services_histories])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getStateRanges()
    {
        $services_names = $this->EquipsNames();

        $services_ranges = [];

        foreach ($services_names as $service) {

            $checks = $this->getServicesChecks()->where('nagios_services.service_object_id', $service->service_object_id)->get();

            if(!empty($checks)) {
                array_push($services_ranges, $checks);
            }

            unset($checks);
        }
        
        return $this->OrganizeStates($services_ranges);
    }

    public function OrganizeStates($services_ranges)
    {
        $services_range_of_states = [];

        foreach ($services_ranges as $service) {
            
            // Get a single equipement checks
            $checks_of_service = $service;
            
            $start_index = 0;
            $end_index = 0;

            if (sizeof($checks_of_service) == 1) {
                // Convert State
                $checks_of_service[0]->state = $this->convertState($checks_of_service[0]->state);
                // push the range in table
                array_push($services_range_of_states, $checks_of_service[0]);
            } else {
                // Search on single equipements checks ranges
                for ($i=0; $i < sizeof($checks_of_service); $i++) {
                    
                    if ($i < (sizeof($checks_of_service)-1)) {

                        if ($checks_of_service[$i]->state == $checks_of_service[$i+1]->state) {
                            $end_index = $i;
                            continue;
                        } else {

                            $end_index = $i;

                            // set end_time of service check to the last end_time of state
                            $checks_of_service[$start_index]->end_time = $checks_of_service[$end_index]->end_time;

                            // Convert State
                            $checks_of_service[$start_index]->state = $this->convertState($checks_of_service[$start_index]->state);

                            // push the range in table
                            array_push($services_range_of_states, $checks_of_service[$start_index]);

                            // reset the start_index var
                            $start_index = $i+1;
                        }

                    } else {
                        if ($checks_of_service[$i]->state == $checks_of_service[$i-1]->state) {

                            // set end_time of service check to the last end_time of state
                            $checks_of_service[$start_index]->end_time = $checks_of_service[$i]->end_time;
                            
                            // Convert State
                            $checks_of_service[$start_index]->state = $this->convertState($checks_of_service[$start_index]->state);

                            // push the range in table
                            array_push($services_range_of_states, $checks_of_service[$start_index]);

                        } else {
                            /**** BEFOR LAST INDEX */
                            // set end_time of service check to the last end_time of state
                            $checks_of_service[$start_index]->end_time = $checks_of_service[$i-1]->end_time;
                            
                            // Convert State
                            $checks_of_service[$start_index]->state = $this->convertState($checks_of_service[$start_index]->state);

                            // push the range in table
                            array_push($services_range_of_states, $checks_of_service[$start_index]);

                            /**** LAST INDEX */
                            // Convert State
                            $checks_of_service[$i]->state = $this->convertState($checks_of_service[$i]->state);

                            // push the range in table
                            array_push($services_range_of_states, $checks_of_service[$i]);
                        }
                    }

                }
            }
            
        }

        return $this->OrderRanges($services_range_of_states);
    }

    public function OrderRanges($ranges)
    {
        usort($ranges, function ($item1, $item2) {
            return $item2->servicecheck_id <=> $item1->servicecheck_id;
        });    
        
        return $ranges;
    }

    public function getServicesChecks()
    {
        
        if ($this->site_name == 'All') {
            
            $services_histories = DB::table('nagios_servicechecks')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                ->where('alias','host')
                ->orderBy('nagios_servicechecks.start_time')
                ->take(20000);
                
        } else {

            $services_histories = DB::table('nagios_servicechecks')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_servicechecks.service_object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id','nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_servicechecks.servicecheck_id','nagios_servicechecks.state','nagios_servicechecks.start_time','nagios_servicechecks.end_time','nagios_servicechecks.output')
                ->where('alias','host')
                ->orderBy('nagios_servicechecks.start_time')
                ->take(20000);

        }
        
        // filter by Date From
        if ($this->date_from)
        {
            $services_histories = $services_histories->where('nagios_servicechecks.start_time','>=',$this->date_from);
        }

        // filter by Date To
        if ($this->date_to)
        {
            $services_histories = $services_histories->where('nagios_servicechecks.start_time','<=', date('Y-m-d', strtotime($this->date_to. ' + 1 days')));
        }

        return $services_histories;
    }

    public function EquipsNames()
    {
        
        if ($this->site_name == 'All') {

            return DB::table('nagios_services')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->select('nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_hosts.host_object_id','nagios_hosts.display_name as host_name')
                ->where('alias','host')
                ->get();

        } else {
        
            return DB::table('nagios_services')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_services.display_name as service_name','nagios_services.service_object_id','nagios_hosts.host_object_id','nagios_hosts.display_name as host_name')
                ->where('alias','host')
                ->get();

        }
    }

    public function getServicesGroups()
    {
        $groups = [];
        $boxes = $this->getBoxes();
        $all_groups = [];
    
        foreach ($boxes as $box) {
    
            $group = [];
    
            foreach ($this->EquipsNames() as $service) {
    
                if($service->host_object_id == $box->host_object_id)
                {
                    array_push($group,$service);
                }
            }
    
            array_push($groups,$group);
        }
    
        $services = [];
    
        for ($i=0; $i < sizeof($groups); $i++) {
    
            foreach ($groups[$i] as $gp) {
    
                array_push($services,$gp->service_name);
    
            }
    
            array_push($all_groups,(object)['host_name' => $groups[$i][0]->host_name, 'services' => $services]);
    
            $services = [];
        }
    
        return $all_groups;
    }

    public function getBoxes()
    {

        if ($this->site_name == 'All') {

            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();

        } else {

            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
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
