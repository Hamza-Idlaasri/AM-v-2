<?php

namespace App\Http\Livewire\Notifications\Items;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Services extends Component
{
    public function render()
    {
        $services = $this->getServicesNotif();

        return view('livewire.notifications.items.services')->with(['services' => $services]);
    }

    public function getServicesNotif()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == "All") {

            return DB::table('nagios_notifications')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->where('nagios_hosts.alias','host')
                ->select('nagios_services.display_name as service_name','nagios_hosts.display_name as host_name','nagios_notifications.*')
                ->where('nagios_notifications.start_time','>', date('Y-m-d H:i:s', strtotime("-1 day")))
                ->orderByDesc('start_time')
                ->get();

        } else {

            return DB::table('nagios_notifications')
                ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
                ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_hosts.alias','host')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_services.display_name as service_name','nagios_hosts.display_name as host_name','nagios_notifications.*')
                ->where('nagios_notifications.start_time','>', date('Y-m-d H:i:s', strtotime("-1 day")))
                ->orderByDesc('start_time')
                ->get();
        }

        
    }
}
