<?php

namespace App\Http\Livewire\Monitoring\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Box extends Component
{
    public $box_id;

    public function mount(Request $request)
    {
        $this->box_id = $request->id;
    }

    public function render()
    {
        $box = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id',$this->box_id)
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->first();

        if(!empty($box))
            $this->convertRetryTime($box);

        return view('livewire.monitoring.details.box')
            ->with(['box' => $box])
            ->extends('layouts.app')
            ->section('content');
    }

    public function convertRetryTime($box)
    {
        $box->retry_interval = round($box->retry_interval * 60,2);
    }
}
