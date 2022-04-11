<?php

namespace App\Http\Livewire\Notifications;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Notif;

class Notifications  extends Component
{
    public $hosts_not_checked = 0;
    public $boxes_not_checked = 0;
    public $services_not_checked = 0;
    public $equips_not_checked = 0;
    
    public $checked_hosts = 0;
    public $checked_boxes = 0;
    public $checked_services = 0;
    public $checked_equips = 0;
    
    public $total_hosts = 0;
    public $total_boxes = 0;
    public $total_services = 0;
    public $total_equips = 0;

    public function checkNotif($val)
    {
        $user = Notif::where('user_id',auth()->user()->id);

        switch ($val) {

            case 'hosts':
                $user->update(['hosts' => $this->total_hosts]);
                break;

            case 'services':
                $user->update(['services' => $this->total_services]);
                break;

            case 'boxes':
                $user->update(['boxes' => $this->total_boxes]);
                break;

            case 'equips':
                $user->update(['equips' => $this->total_equips]);
                break;
        }
    }

    public function render()
    {
        $this->getTotal_HostsNotifs();
        $this->getTotal_ServicesNotifs();
        $this->getTotal_BoxesNotifs();
        $this->getTotal_EquipsNotifs();

        $this->ElementsChecked();

        $this->hosts_not_checked = $this->total_hosts - $this->checked_hosts;
        $this->boxes_not_checked = $this->total_boxes - $this->checked_boxes;
        $this->services_not_checked = $this->total_services - $this->checked_services;
        $this->equips_not_checked = $this->total_equips - $this->checked_equips;

        $total = $this->hosts_not_checked + $this->services_not_checked + $this->equips_not_checked + $this->boxes_not_checked;

        return view('livewire.notifications.notifications')
            ->with(['hosts_not_checked' => $this->hosts_not_checked, 'services_not_checked' => $this->services_not_checked, 'boxes_not_checked' => $this->boxes_not_checked, 'equips_not_checked' => $this->equips_not_checked,'total' => $total])
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
        
        $this->total_services = 0;

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
