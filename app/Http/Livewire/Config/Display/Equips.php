<?php

namespace App\Http\Livewire\Config\Display;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Equips extends Component
{
    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $equips = $this->getEquips()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->get();

        } else {

            $equips = $this->getEquips()->get();

        }

        if (!empty($equips)){
            $this->convertRetryTime($equips);
            $this->fixInputNbr($equips);
        }

        return view('livewire.config.display.equips')
            ->with('equips', $equips)
            ->extends('layouts.app')
            ->section('content');
    }

    public function getEquips()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == "All") {
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_id','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.normal_check_interval','nagios_servicestatus.retry_check_interval','nagios_servicestatus.max_check_attempts','nagios_servicestatus.has_been_checked','nagios_servicestatus.notifications_enabled','nagios_servicestatus.check_command')
                ->orderBy('nagios_hosts.display_name');
        } else {
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_id','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.normal_check_interval','nagios_servicestatus.retry_check_interval','nagios_servicestatus.max_check_attempts','nagios_servicestatus.has_been_checked','nagios_servicestatus.notifications_enabled','nagios_servicestatus.check_command')
                ->orderBy('nagios_hosts.display_name');
        }
        
    }

    public function convertRetryTime($equips)
    {
        foreach ($equips as $equip) {
            $equip->retry_check_interval = round($equip->retry_check_interval * 60,2);
        }
    }

    public function fixInputNbr($equips)
    {
        foreach ($equips as $equip) {
            $equip->check_command = substr($equip->check_command,7,-2);
        }
    }
}

