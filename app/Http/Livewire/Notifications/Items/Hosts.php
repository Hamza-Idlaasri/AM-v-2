<?php

namespace App\Http\Livewire\Notifications\Items;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Hosts extends Component
{
    public function render()
    {
        $hosts = $this->getHostsNotif();
        
        return view('livewire.notifications.items.hosts')->with(['hosts' => $hosts]);
    }

    public function getHostsNotif()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == "All") {

            return DB::table('nagios_notifications')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
                ->where('nagios_hosts.alias','host')
                ->select('nagios_hosts.display_name as host_name','nagios_notifications.*')
                ->where('nagios_notifications.start_time','>', date('Y-m-d H:i:s', strtotime("-1 day")))
                ->orderByDesc('start_time')
                ->get();

        } else {

            return DB::table('nagios_notifications')
                ->join('nagios_customvariables','nagios_notifications.object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hosts','nagios_notifications.object_id','=','nagios_hosts.host_object_id')
                ->where('nagios_hosts.alias','host')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_notifications.*')
                ->where('nagios_notifications.start_time','>', date('Y-m-d H:i:s', strtotime("-1 day")))
                ->orderByDesc('start_time')
                ->get();

        }

        
    }
}
