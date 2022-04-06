<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notif;
use Illuminate\Support\Facades\DB;

class NotifsCheck extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $connection = 'am';

    public $total_hosts = 0;
    public $total_boxes = 0;
    public $total_services = 0;
    public $total_equips = 0;

    public function run()
    {
        $users = User::all();

        $this->getTotal_HostsNotifs();
        $this->getTotal_BoxesNotifs();
        $this->getTotal_ServicesNotifs();
        $this->getTotal_EquipsNotifs();

        foreach ($users as $user) {

            $notifs = Notif::create([
                'user_id' => $user->id,
                'hosts' => $this->total_hosts,
                'services' => $this->total_services,
                'boxes' => $this->total_boxes,
                'equips' => $this->total_equips,
            ]);

        }

    }

    public function getTotal_HostsNotifs()
    {
        $hosts = DB::connection('mysql')->table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','host')
            ->select('nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();   
        
        foreach ($hosts as $host) {
            $this->total_hosts++;
        }
    }

    public function getTotal_ServicesNotifs()
    {
        $services = DB::connection('mysql')->table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->select('nagios_services.display_name as service_name','nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
        
        foreach ($services as $service) {
            $this->total_services++;
        }
    }

    public function getTotal_BoxesNotifs()
    {
        $boxes = DB::connection('mysql')->table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
        
        foreach ($boxes as $box) {
            $this->total_boxes++;
        }
    }

    public function getTotal_EquipsNotifs()
    {
        $equips = DB::connection('mysql')->table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();

        foreach ($equips as $equip) {
            $this->total_equips++;
        }
    }
}
