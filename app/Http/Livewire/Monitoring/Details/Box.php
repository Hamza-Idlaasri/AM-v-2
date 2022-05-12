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

        return view('livewire.monitoring.details.box')
            ->with(['box' => $box])
            ->extends('layouts.app')
            ->section('content');
    }
}
