<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use App\Models\EquipsDetail;

class Overview extends Component
{
    public $hosts_up = 0;
    public $hosts_down = 0;
    public $hosts_unreachable = 0;
    
    public $boxes_up = 0;
    public $boxes_down = 0;
    public $boxes_unreachable = 0;

    public $services_ok = 0;
    public $services_warning = 0;
    public $services_critical = 0;
    public $services_unknown = 0;

    public $equips_ok = 0;
    public $equips_warning = 0;
    public $equips_critical = 0;
    public $equips_unknown = 0;

    public function hosts($site_name)
    {
        if ($site_name == 'All') {
            
            $hosts_summary = DB::table('nagios_hoststatus')
                ->join('nagios_hosts','nagios_hoststatus.host_object_id','=','nagios_hosts.host_object_id')
                ->where('alias','host')
                ->select('nagios_hoststatus.current_state')
                ->get();
        }
        else
        {
            $hosts_summary = DB::table('nagios_hoststatus')
                ->join('nagios_hosts','nagios_hoststatus.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('alias','host')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hoststatus.current_state')
                ->get();
        }
        

        foreach ($hosts_summary as $host) {
            
            switch ($host->current_state) {

                case 0:
                    $this->hosts_up++;
                    break;
                
                case 1:
                    $this->hosts_down++;
                    break;
                
                case 2:
                    $this->hosts_unreachable++;
                    break;

                default:
                    break;
            }

        }

    }
    public function boxes($site_name)
    {
        if ($site_name == 'All') {
            
            $boxes_summary = DB::table('nagios_hoststatus')
            ->join('nagios_hosts','nagios_hoststatus.host_object_id','=','nagios_hosts.host_object_id')
            ->where('alias','box')
            ->select('nagios_hoststatus.current_state')
            ->get();
        }
        else
        {
            $boxes_summary = DB::table('nagios_hoststatus')
                ->join('nagios_hosts','nagios_hoststatus.host_object_id','=','nagios_hosts.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->where('alias','box')
                ->select('nagios_hoststatus.current_state')
                ->get();
        }

        foreach ($boxes_summary as $box) {
            
            switch ($box->current_state) {

                case 0:
                    $this->boxes_up++;
                    break;
                
                case 1:
                    $this->boxes_down++;
                    break;
                
                case 2:
                    $this->boxes_unreachable++;
                    break;

                default:
                    break;
            }

        }

    }

    public function services($site_name)
    {
        if ($site_name == 'All') {
            
            $services_summary = DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->select('nagios_servicestatus.current_state')
                ->get();
        }
        else
        {
            $services_summary = DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_servicestatus.current_state')
                ->get();
        }
        
        foreach ($services_summary as $service) {

            switch ($service->current_state) {
                case 0:
                    $this->services_ok++;
                    break;
                
                case 1:
                    $this->services_warning++;
                    break;
                
                case 2:
                    $this->services_critical++;
                    break;
                    
                case 3:
                    $this->services_unknown++;
                    break;

                default:
                    break;
            }
        }
    }

    public function equips($site_name)
    {
        if ($site_name == 'All') {
            
            $equips_summary = DB::table('nagios_services')
                ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
                ->join('am.equips_details as ed', function ($join) {
                    $join->on('nagios_services.display_name','=','ed.pin_name')
                        ->on('nagios_hosts.display_name','=','ed.box_name');
                })
                ->join('nagios_servicestatus','nagios_services.service_object_id','nagios_servicestatus.service_object_id')
                ->select('nagios_servicestatus.current_state')
                ->get();
        }
        else
        {
            $equips_summary = DB::table('nagios_services')
                ->join('nagios_hosts','nagios_services.host_object_id','=','nagios_hosts.host_object_id')
                ->join('am.equips_details as ed', function ($join) {
                    $join->on('nagios_services.display_name','=','ed.pin_name')
                        ->on('nagios_hosts.display_name','=','ed.box_name');
                })
                ->join('nagios_servicestatus','nagios_services.service_object_id','nagios_servicestatus.service_object_id')
                ->where('ed.site_name',$site_name)
                ->select('nagios_servicestatus.current_state')
                ->get();
        }

        foreach ($equips_summary as $equip) {

            switch ($equip->current_state) {

                case 0:
                    $this->equips_ok++;
                    break;
                
                case 1:
                    $this->equips_warning++;
                    break;
                
                case 2:
                    $this->equips_critical++;
                    break;
                    
                case 3:
                    $this->equips_unknown++;
                    break;

                default:
                    break;
            }
        }
    }

    public function render()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $this->hosts($site_name);
        $this->boxes($site_name);
        $this->services($site_name);
        $this->equips($site_name);

        return view('livewire.overview')
        ->with(['hosts_up' => $this->hosts_up,'hosts_down' => $this->hosts_down,'hosts_unreachable' => $this->hosts_unreachable,'boxes_up' => $this->boxes_up,'boxes_down' => $this->boxes_down,'boxes_unreachable' => $this->boxes_unreachable,'services_ok' => $this->services_ok,'services_warning' => $this->services_warning,'services_critical' => $this->services_critical,'services_unknown' => $this->services_unknown,'equips_ok' => $this->equips_ok,'equips_warning' => $this->equips_warning,'equips_critical' => $this->equips_critical,'equips_unknown' => $this->equips_unknown])
        ->extends('layouts.app')
        ->section('content');
    }
}
