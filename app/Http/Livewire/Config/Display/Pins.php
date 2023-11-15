<?php

namespace App\Http\Livewire\Config\Display;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Pins extends Component
{
    public $search, $site_name;

    protected $queryString = ['search'];

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        if ($this->search) {
            $pins = $this->getEquips()
                ->where('nagios_hosts.display_name', 'like', '%' . $this->search . '%')
                ->get();
        } else {

            $pins = $this->getEquips()->get();
        }

        if (!empty($pins)) {
            $this->convertRetryTime($pins);
            $this->fixInputNbr($pins);
        }

        return view('livewire.config.display.pins')
            ->with(['pins' => $pins, 'site_name' => $this->site_name])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getEquips()
    {

        if ($this->site_name == "All") {

            return DB::table('nagios_hosts')
                ->where('alias', 'box')
                ->join('nagios_services', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', '=', 'nagios_servicestatus.service_object_id')
                ->join('am.equips_details as ed', function ($join) {
                    $join->on('nagios_services.display_name','=','ed.pin_name')
                        ->on('nagios_hosts.display_name','=','ed.box_name');
                })
                ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.host_object_id', 'nagios_services.display_name as pin_name', 'nagios_services.service_id', 'nagios_services.service_object_id', 'nagios_servicestatus.current_state', 'nagios_servicestatus.output', 'nagios_servicestatus.normal_check_interval', 'nagios_servicestatus.retry_check_interval', 'nagios_servicestatus.max_check_attempts', 'nagios_servicestatus.has_been_checked', 'nagios_servicestatus.notifications_enabled', 'nagios_servicestatus.check_command', 'ed.equip_name', 'ed.site_name')
                ->orderBy('nagios_hosts.display_name')
                ->orderBy('nagios_services.display_name');
        } else {
            
            return DB::table('nagios_hosts')
                ->where('alias', 'box')
                ->join('nagios_services', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', '=', 'nagios_servicestatus.service_object_id')
                ->join('am.equips_details as ed', function ($join) {
                    $join->on('nagios_services.display_name','=','ed.pin_name')
                        ->on('nagios_hosts.display_name','=','ed.box_name');
                })
                ->where('ed.site_name', $this->site_name)
                ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.host_object_id', 'nagios_services.display_name as pin_name', 'nagios_services.service_id', 'nagios_services.service_object_id', 'nagios_servicestatus.current_state', 'nagios_servicestatus.output', 'nagios_servicestatus.normal_check_interval', 'nagios_servicestatus.retry_check_interval', 'nagios_servicestatus.max_check_attempts', 'nagios_servicestatus.has_been_checked', 'nagios_servicestatus.notifications_enabled', 'nagios_servicestatus.check_command', 'ed.equip_name','ed.site_name')
                ->orderBy('nagios_hosts.display_name')
                ->orderBy('nagios_services.display_name');
        }
    }

    public function convertRetryTime($equips)
    {
        foreach ($equips as $equip) {
            $equip->normal_check_interval = round($equip->normal_check_interval * 60, 2);
            $equip->retry_check_interval = round($equip->retry_check_interval * 60, 2);
        }
    }


    public function fixInputNbr($equips)
    {
        foreach ($equips as $equip) {
            $equip->check_command = substr($equip->check_command, 9, -2);
        }
    }
}
