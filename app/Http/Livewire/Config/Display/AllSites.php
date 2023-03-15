<?php

namespace App\Http\Livewire\Config\Display;

use Livewire\Component;
use App\Models\Sites;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;
use Illuminate\Support\Facades\DB;

class AllSites extends Component
{
    public function render()
    {
        $all_sites = Sites::all()->except(1);

        $sites = [];

        foreach ($all_sites as $site) {
            
            $hosts_count = 0;
            $services_count = 0;
            $boxes_count = 0;
            $equips_count = 0;
            $pins_count = 0;

            // Count Hosts
            $hosts = $this->getHosts($site->site_name);
            // Counter
            foreach ($hosts as $host) {
                $hosts_count++;
            }

            // Count Services
            $services = $this->getServices($site->site_name);
            // Counter
            foreach ($services as $service) {
                $services_count++;
            }

            // Count Boxes
            $boxes = $this->getBoxes($site->site_name);
            // Counter
            foreach ($boxes as $box) {
                $boxes_count++;
            }

            // Count Equips
            $equips = $this->getEquips($site->site_name);
            // Counter
            foreach ($equips as $equip) {
                $equips_count++;
            }

            // Count Pins
            $pins = $this->getPins($site->site_name);
            // Counter
            foreach ($pins as $pin) {
                $pins_count++;
            }

            array_push($sites,(object)['id' => $site->id,'site_name' => $site->site_name,'hosts' => $hosts_count,'services' => $services_count,'boxes' => $boxes_count,'equips' => $equips_count,'pins' => $pins_count]);
        }

        return view('livewire.config.display.all-sites')
            ->with(['sites' => $sites])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getHosts($site_name)
    {
        return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.host_object_id')
                ->get();
    }

    public function getServices($site_name)
    {
        return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->select('nagios_services.service_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->get();
    }

    public function getBoxes($site_name)
    {
        return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.host_object_id')
                ->orderBy('display_name')
                ->get();
    }

    public function getEquips($site_name)
    {
        return EquipsNames::where('site_name',$site_name)->get();
    }
    
    public function getPins($site_name)
    {
        return EquipsDetail::where('site_name',$site_name)->get();
    }
}
