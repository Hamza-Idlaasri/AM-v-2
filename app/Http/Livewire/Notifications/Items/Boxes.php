<?php

namespace App\Http\Livewire\Notifications\Items;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Boxes extends Component
{
    public function render()
    {
        $boxes = $this->getBoxesNotif();

        return view('livewire.notifications.items.boxes')->with(['boxes' => $boxes]);
    }

    public function getBoxesNotif()
    {
        return DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
    }
}
