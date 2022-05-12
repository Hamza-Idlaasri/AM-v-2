<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UsersSite;
use App\Models\Sites;
use App\Models\Notif; 
use Illuminate\Support\Facades\DB;

class Registration extends Component
{
    public $name;
    public $email;
    public $phone_number;
    public $password;
    public $password_confirmation;
    public $site;
 
    public $total_hosts = 0;
    public $total_boxes = 0;
    public $total_services = 0;
    public $total_equips = 0;
    
    protected $rules = [
        'name' => 'required|min:3|max:15|unique:am.users|regex:/^[a-zA-Z][a-zA-Z0-9-_(). ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/',
        'email' => 'required|email|max:100|unique:am.users',
        'phone_number' => 'required|regex:/[0-9]{9}/',
        'password' => 'required|string|confirmed|min:5|max:12|regex:/^[a-zA-Z0-9-_().@$=%&#+{}*ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/|unique:am.users',
        'site' => 'required'
    ];

    // public function updated($propertyName)
    // {
    //     $this->validateOnly($propertyName);
    // }

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

        ])->attachRole('user');

        // Add the user to a site
        UsersSite::create([
            'user_id' => User::all()->last()->id,
            'site_name' => $this->site,
            'current_site' => $this->site
        ]);

        $this->AddNotifs();

        $notif = Notif::create([
            'user_id' => User::all()->last()->id,
            'hosts' => $this->total_hosts,
            'services' => $this->total_services,
            'boxes' => $this->total_boxes,
            'equips' => $this->total_equips,
            // 'read_at->hosts' => '2020-01-01 00:00:00',
            // 'read_at->services' => '2020-01-01 00:00:00',
            // 'read_at->boxes' => '2020-01-01 00:00:00',
            // 'read_at->equips' => '2020-01-01 00:00:00'
        ]);
        
        return redirect()->route('config.users');
    }

    public function render()
    {
        $all_sites = Sites::all();

        return view('livewire.auth.registration')
            ->with(['all_sites' => $all_sites])
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
        // $date = date('Y-m-d H:i:s', strtotime("-1 days"));

        $site_name = UsersSite::where('user_id',User::all()->last()->id)->first()->current_site;

        $hosts = DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_hosts.alias','host')
            // ->where('nagios_notifications.start_time','>',$date)
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();   
            
        foreach ($hosts as $host) {
            $this->total_hosts++;
        }
    }

    public function getTotal_ServicesNotifs()
    {
        // $date = date('Y-m-d H:i:s', strtotime("-1 days"));
        
        $site_name = UsersSite::where('user_id',User::all()->last()->id)->first()->current_site;

        $services = DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_hosts.alias','host')
            ->where('nagios_customvariables.varvalue',$site_name)
            // ->where('nagios_notifications.start_time','>',$date)
            ->select('nagios_services.display_name as service_name','nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
        
        foreach ($services as $service) {
            $this->total_services++;
        }
    }

    public function getTotal_BoxesNotifs()
    {
        // $date = date('Y-m-d H:i:s', strtotime("-1 days"));

        $site_name = UsersSite::where('user_id',User::all()->last()->id)->first()->current_site;

        $boxes = DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_customvariables.varvalue',$site_name)
            // ->where('nagios_notifications.start_time','>',$date)
            ->select('nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
        
        foreach ($boxes as $box) {
            $this->total_boxes++;
        }
    }

    public function getTotal_EquipsNotifs()
    {
        // $date = date('Y-m-d H:i:s', strtotime("-1 days"));

        $site_name = UsersSite::where('user_id',User::all()->last()->id)->first()->current_site;

        $equips = DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_customvariables.varvalue',$site_name)
            // ->where('nagios_notifications.start_time','>',$date)
            ->select('nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();

        foreach ($equips as $equip) {
            $this->total_equips++;
        }
    }
}
