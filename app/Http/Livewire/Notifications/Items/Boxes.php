<?php

namespace App\Http\Livewire\Notifications\Items;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Boxes extends Component
{
    public function render()
    {
        $boxes = $this->getBoxesNotif();

        return view('livewire.notifications.items.boxes')->with(['boxes' => $boxes]);
    }

    public function getBoxesNotif()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->orderByDesc('start_time')
            ->get();
    }
}
