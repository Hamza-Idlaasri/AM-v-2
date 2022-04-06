<?php

namespace App\Http\Livewire\Grid;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Topbar extends Component
{
    public $total_hosts = 0;
    public $hosts_up = 0;
    public $hosts_down = 0;
    public $hosts_unreachable = 0;
    
    public $total_boxes = 0;
    public $boxes_up = 0;
    public $boxes_down = 0;
    public $boxes_unreachable = 0;
    
    public $total_services = 0;
    public $services_ok = 0;
    public $services_warning = 0;
    public $services_critical = 0;
    public $services_unknown = 0;
    
    public $total_equips = 0;
    public $equips_ok = 0;
    public $equips_warning = 0;
    public $equips_critical = 0;
    public $equips_unknown = 0;

    public function render()
    {
        $this->getHosts();
        $this->getBoxes();
        $this->getServices();
        $this->getEquips();

        return view('livewire.grid.topbar')
            ->with([
                'total_hosts' => $this->total_hosts,
                'hosts_up' => $this->hosts_up,
                'hosts_down' => $this->hosts_down,
                'hosts_unreachable' => $this->hosts_unreachable,
                'total_boxes' => $this->total_boxes,
                'boxe_up' => $this->boxes_up,
                'boxes_down' => $this->boxes_down,
                'boxes_unreachable' => $this->boxes_unreachable,

                'total_services' => $this->total_services,
                'services_ok' => $this->services_ok,
                'services_warning' => $this->services_warning,
                'services_critical' => $this->services_critical,
                'services_unknown' => $this->services_unknown,

                'total_equips' => $this->total_equips,
                'equips_ok' => $this->equips_ok,
                'equips_warning' => $this->equips_warning,
                'equips_critical' => $this->equips_critical,
                'equips_unknown' => $this->equips_unknown
            ]);
    }

    public function getHosts()
    {
        $hosts_summary = DB::table('nagios_hoststatus')
            ->join('nagios_hosts','nagios_hoststatus.host_object_id','=','nagios_hosts.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->get();

        $this->hosts_up = 0;
        $this->hosts_down = 0;
        $this->hosts_unreachable = 0;
        $this->total_hosts = 0;

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
            }

            $this->total_hosts++;
        }

        if ($this->total_hosts > 1000) {
        }

    }

    public function getServices()
    {
        $services_summary = DB::table('nagios_hosts')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->where('nagios_hosts.alias','host')
            ->get();

        $this->services_ok = 0;
        $this->services_warning = 0;
        $this->services_critical = 0;
        $this->services_unknown = 0;
        $this->total_services = 0;

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
            }

            $this->total_services++;
        }
           
    }

    public function getBoxes()
    {
        $boxes_summary = DB::table('nagios_hoststatus')
            ->join('nagios_hosts','nagios_hoststatus.host_object_id','=','nagios_hosts.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->get();

        $this->boxes_up = 0;
        $this->boxes_down = 0;
        $this->boxes_unreachable = 0;
        $this->total_boxes = 0;

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
            }

            $this->total_boxes++;
        }
    }

    public function getEquips()
    {
        $equips_summary = DB::table('nagios_hosts')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->where('nagios_hosts.alias','box')
            ->get();
        
        $this->equips_ok = 0;
        $this->equips_warning = 0;
        $this->equips_critical = 0;
        $this->equips_unknown = 0;
        $this->total_equips = 0;

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
            }

            $this->total_equips++;
        }
    }
}
