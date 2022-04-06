<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 
use App\Models\Notif; 
use Illuminate\Support\Facades\DB;

class Registration extends Component
{
    public $name;
    public $email;
    public $phone_number;
    public $password;
    public $password_confirmation;
 
    protected $rules = [
        'name' => 'required|min:3|max:15|unique:am.users|regex:/^[a-zA-Z][a-zA-Z0-9-_(). ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/',
        'email' => 'required|email|max:100|unique:am.users',
        'phone_number' => 'required|regex:/[0-9]{9}/',
        'password' => 'required|string|confirmed|min:5|max:12|regex:/^[a-zA-Z0-9-_().@$=%&#+{}*ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/|unique:am.users',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function register()
    {
        // Validate user info
        $this->validate();
  
        // Store User 
        User::create([

            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => '+212'.$this->phone_number,
            'notified' => 0,
            'password' => Hash::make($this->password),

        ])->attachRole('superviseur');

        $this->AddNotifs();

        $notif = Notif::create([
            'user_id' => User::all()->last()->id,
            'hosts' => $this->total_hosts,
            'services' => $this->total_services,
            'boxes' => $this->total_boxes,
            'equips' => $this->total_equips,
        ]);
        
        return redirect()->route('config.users');
    }

    public $total_hosts = 0;
    public $total_boxes = 0;
    public $total_services = 0;
    public $total_equips = 0;

    public function render()
    {
        return view('livewire.auth.registration')
            ->extends('layouts.auth')
            ->section('content');
    }

    public function AddNotifs()
    {
        $this->getTotal_HostsNotifs();
        $this->getTotal_BoxesNotifs();
        $this->getTotal_ServicesNotifs();
        $this->getTotal_EquipsNotifs();
    }

    public function getTotal_HostsNotifs()
    {
        $date =date('Y-m-d H:i:s', strtotime("-1 days"));

        $hosts = DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','host')
            ->where('nagios_notifications.start_time','>',$date)
            ->select('nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();   
        
        foreach ($hosts as $host) {
            $this->total_hosts++;
        }
    }

    public function getTotal_ServicesNotifs()
    {
        $date =date('Y-m-d H:i:s', strtotime("-1 days"));
        
        $services = DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->where('nagios_notifications.start_time','>',$date)
            ->select('nagios_services.display_name as service_name','nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
        
        foreach ($services as $service) {
            $this->total_services++;
        }
    }

    public function getTotal_BoxesNotifs()
    {
        $date =date('Y-m-d H:i:s', strtotime("-1 days"));

        $boxes = DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_notifications.start_time','>',$date)
            ->select('nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
        
        foreach ($boxes as $box) {
            $this->total_boxes++;
        }
    }

    public function getTotal_EquipsNotifs()
    {
        $date =date('Y-m-d H:i:s', strtotime("-1 days"));

        $equips = DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_notifications.start_time','>',$date)
            ->select('nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();

        foreach ($equips as $equip) {
            $this->total_equips++;
        }
    }
}
