<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Notif;
use App\Models\User;
use App\Mail\HostMail;
use Illuminate\Support\Facades\Mail;

class PopupNotifs extends Component
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

    public function hide($element,$end_time)
    {
        $user = Notif::where('user_id',auth()->user()->id)->first();
        
        switch ($element) {

            case 'hosts':
                    $user->update([
                        "read_hosts_at" => $end_time,
                    ]);
                break;
                
            case 'services':
                    $user->update([
                        "read_services_at" => $end_time,
                    ]);
                break;

            case 'boxes':
                    $user->update([
                        "read_boxes_at" => $end_time,
                    ]);
                break;

            case 'equips':
                    $user->update([
                        "read_equips_at" => $end_time,
                    ]);
                break;

        }

    }

    public function render()
    {
        // Notifs of all elements
        $hosts = $this->getTotal_HostsNotifs();
        $services = $this->getTotal_ServicesNotifs();
        $boxes = $this->getTotal_BoxesNotifs();
        $equips = $this->getTotal_EquipsNotifs();

        // Get user currently login
        $user = Notif::where('user_id',auth()->user()->id)->first();

        if (sizeof($hosts)) 
        {
            $read_at = date('Y-m-d H:i:s', strtotime($user->read_hosts_at));
            $last_notif = date('Y-m-d H:i:s', strtotime($hosts[0]->end_time));

            if ($read_at < $last_notif) {
                session()->flash('hosts_notifs', 'test');
            }

        }

        if (sizeof($services)) 
        {   
            $read_at = date('Y-m-d H:i:s', strtotime($user->read_services_at));
            $last_notif = date('Y-m-d H:i:s', strtotime($services[0]->end_time));

            if ($read_at < $last_notif) {
                session()->flash('services_notifs', 'test');
            }
        }

        if (sizeof($boxes)) 
        { 
            $read_at = date('Y-m-d H:i:s', strtotime($user->read_boxes_at));
            $last_notif = date('Y-m-d H:i:s', strtotime($boxes[0]->end_time));

            if ($read_at < $last_notif) {
                session()->flash('boxes_notifs', 'test');
            }
        }

        if (sizeof($equips)) 
        {    
            $read_at = date('Y-m-d H:i:s', strtotime($user->read_equips_at));
            $last_notif = date('Y-m-d H:i:s', strtotime($equips[0]->end_time));

            if ($read_at < $last_notif) {
                session()->flash('equips_notifs', 'test');
            }
        }

        return view('livewire.popup-notifs')->with(['hosts' => $hosts,'services' => $services,'boxes' => $boxes,'equips' => $equips]);
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

        $this->ElementsChecked();

        $this->hosts_not_checked = $this->total_hosts - $this->checked_hosts;

        return DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','host')
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.address','nagios_notifications.*')
            ->orderBy('start_time')
            ->take($this->hosts_not_checked)
            ->get();
    }

    public function getTotal_ServicesNotifs()
    {
        $services = DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->select('nagios_services.display_name as service_name','nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderBy('start_time')
            ->get();
        
        $this->total_services = 0;

        foreach ($services as $service) {
            $this->total_services++;
        }

        $this->ElementsChecked();

        $this->services_not_checked = $this->total_services - $this->checked_services;

        return DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->select('nagios_services.display_name as service_name','nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderBy('start_time')
            ->take($this->services_not_checked)
            ->get();
    }

    public function getTotal_BoxesNotifs()
    {
        $boxes = DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderBy('start_time')
            ->get();
        
        $this->total_boxes = 0;

        foreach ($boxes as $box) {
            $this->total_boxes++;
        }

        $this->ElementsChecked();

        $this->boxes_not_checked = $this->total_boxes - $this->checked_boxes;

        return DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderBy('start_time')
            ->take($this->boxes_not_checked)
            ->get();

    }

    public function getTotal_EquipsNotifs()
    {
        $equips = DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderBy('start_time')
            ->get();

        $this->total_equips = 0;

        foreach ($equips as $equip) {
            $this->total_equips++;
        }

        $this->ElementsChecked();

        $this->equips_not_checked = $this->total_equips - $this->checked_equips;

        return DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderBy('start_time')
            ->take($this->equips_not_checked)
            ->get();

    }

    public function ElementsChecked()
    {
        $this->checked_hosts = 0;
        $this->checked_boxes = 0;
        $this->checked_services = 0;
        $this->checked_equips = 0;

        $elements_checked = Notif::where('user_id',auth()->user()->id)->get();

        foreach ($elements_checked as $element) {
            $this->checked_hosts = $element->hosts;
            $this->checked_services = $element->services;
            $this->checked_boxes = $element->boxes;
            $this->checked_equips = $element->equips;
        }
        
    }

}
