<?php

namespace App\Http\Livewire\Monitoring\Details;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Service extends Component
{
    public $service_id;

    public function mount(Request $request)
    {
        $this->service_id = $request->id;
    }

    public function render()
    {
        $service = DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.*','nagios_services.display_name as service_name','nagios_services.*','nagios_servicestatus.*')
            ->where('nagios_services.service_object_id',$this->service_id)
            ->first();

        if(!empty($service))
            $this->convertRetryTime($service);

        return view('livewire.monitoring.details.service')
            ->with(['service' => $service])
            ->extends('layouts.app')
            ->section('content');
    }

    public function convertRetryTime($service)
    {
        $service->retry_interval = round($service->retry_interval * 60,2);
    }
}
