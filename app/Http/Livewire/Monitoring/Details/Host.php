<?php

namespace App\Http\Livewire\Monitoring\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Host extends Component
{
    public $host_id;

    public function mount(Request $request)
    {
        $this->host_id = $request->id;
    }

    public function render()
    {
        $host = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id',$this->host_id)
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->first();

        if(!empty($host))
            $this->convertRetryTime($host);
            
        return view('livewire.monitoring.details.host')
            ->with(['host' => $host])
            ->extends('layouts.app')
            ->section('content');
    }

    public function convertRetryTime($host)
    {
        $host->retry_interval = round($host->retry_interval * 60,2);
    }
}
