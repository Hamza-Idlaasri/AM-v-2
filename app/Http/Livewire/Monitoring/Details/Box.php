<?php

namespace App\Http\Livewire\Monitoring\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Box extends Component
{
    public $box;

    public function mount(Request $request)
    {
        $this->box = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id',$request->id)
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->get();
    }

    public function render()
    {
        return view('livewire.monitoring.details.box')
            ->with(['box' => $this->box])
            ->extends('layouts.app')
            ->section('content');
    }
}
