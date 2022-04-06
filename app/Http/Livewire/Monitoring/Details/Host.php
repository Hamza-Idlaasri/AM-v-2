<?php

namespace App\Http\Livewire\Monitoring\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Host extends Component
{
    public $host;

    public function mount(Request $request)
    {
        $this->host = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id',$request->id)
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->get();
    }

    public function render()
    {
        return view('livewire.monitoring.details.host')
            ->with(['host' => $this->host])
            ->extends('layouts.app')
            ->section('content');
    }
}
