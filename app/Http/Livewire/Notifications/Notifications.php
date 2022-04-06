<?php

namespace App\Http\Livewire\Notifications;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Notif;

class Notifications  extends Component
{
    public $total_hosts = 0;
    public $total_boxes = 0;
    public $total_services = 0;
    public $total_equips = 0;
    
    public $checked_hosts = 0;
    public $checked_boxes = 0;
    public $checked_services = 0;
    public $checked_equips = 0;

    public function checkNotif($val)
    {
        switch ($val) {

            case 'hosts':
                $this->total_hosts = 0;
                break;

            case 'services':
                $this->total_services = 0;
                
                break;

            case 'boxes':
                $this->total_boxes = 0;
                
                break;

            case 'equips':
                $this->total_equips = 0;
                
                break;
        }
    }

    public function render()
    {
        $this->getTotal_HostsNotifs();
        $this->getTotal_BoxesNotifs();
        $this->getTotal_ServicesNotifs();
        $this->getTotal_EquipsNotifs();
        // $this->ElementsChecked();

        // $this->total_hosts = $this->total_hosts - $this->checked_hosts;
        // $this->total_services = $this->total_services - $this->checked_services;
        // $this->total_boxes = $this->total_boxes - $this->checked_boxes;
        // $this->total_equips = $this->total_equips - $this->checked_equips;

        $total = $this->total_hosts + $this->total_services + $this->total_boxes + $this->total_equips;

        return view('livewire.notifications.notifications')
            ->with(['total_hosts' => $this->total_hosts, 'total_services' => $this->total_services, 'total_boxes' => $this->total_boxes, 'total_equips' => $this->total_equips,'total' => $total])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getTotal_HostsNotifs()
    {
        $hosts = DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','host')
            ->select('nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();   
        
        $this->total_hosts = 0;

        foreach ($hosts as $host) {
            $this->total_hosts++;
        }
    }

    public function getTotal_ServicesNotifs()
    {
        $services = DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->select('nagios_services.display_name as service_name','nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
        
        $this->total_services;

        foreach ($services as $service) {
            $this->total_services++;
        }
    }

    public function getTotal_BoxesNotifs()
    {
        $boxes = DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
        
        $this->total_boxes = 0;

        foreach ($boxes as $box) {
            $this->total_boxes++;
        }
    }

    public function getTotal_EquipsNotifs()
    {
        $equips = DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();

        $this->total_equips = 0;

        foreach ($equips as $equip) {
            $this->total_equips++;
        }
    }

    public function ElementsChecked()
    {
        $elements_checked = Notif::where('user_id',auth()->user()->id)->get();

        foreach ($elements_checked as $element) {
            $this->checked_hosts = $element->hosts;
            $this->checked_services = $element->services;
            $this->checked_boxes = $element->boxes;
            $this->checked_equips = $element->equips;
        }
        
    }

}
