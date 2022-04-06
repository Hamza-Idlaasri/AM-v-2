<?php

namespace App\Http\Livewire\Notifications\Items;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Hosts extends Component
{
    public function render()
    {
        $hosts = $this->getHostsNotif();
        
        return view('livewire.notifications.items.hosts')->with(['hosts' => $hosts]);
    }

    public function getHostsNotif()
    {
        return DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','host')
            ->select('nagios_hosts.display_name as host_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
    }
}
